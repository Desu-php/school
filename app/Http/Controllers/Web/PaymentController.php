<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\ChatExpert;
use App\Models\Course;
use App\Models\CourseModuleTest;
use App\Models\CourseTariff;
use App\Models\Lesson;
use App\Models\LessonBlock;
use App\Models\PaymentHistory;
use App\Models\Task;
use App\Models\User;
use App\Models\UserCourse;
use App\Models\UserCourseModuleTest;
use App\Models\UserLesson;
use App\Models\UserLessonBlock;
use App\Models\UserModule;
use App\Models\UserTask;
use App\Notifications\PaymentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PaymentController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'course_tariff_id' => 'required|exists:course_tariffs,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        DB::beginTransaction();

        try {
            $auth = auth('api')->user();
            $data = $request->only( 'course_id', 'course_tariff_id');
            $data['user_id'] = $auth->id;

            if (UserCourse::where('course_id', $data['course_id'])->where('user_id', auth('api')->id())->first()){
                return response()->json([
                    'message' => 'You have already bought this course!',
                    'error' => 'course_bought',
                ], 403);
            }

            $tariff = CourseTariff::where('id', $data['course_tariff_id'])->first();
            $data['expiry_date'] = Carbon::now()->addDays($tariff->duration);
            $user_course = UserCourse::create($data);

            $course = Course::where('id', $request->course_id)->with('course_modules', 'course_chat')->first();
            $moduleData = [];
            $lessonsData = [];
            $courseModuleTests = [];
            $courseModuleTestsData = [];
            $taskData = [];
            $lessonBlockData = [];
            foreach ($course->course_modules as $index => $module) {
                $lessonsData = array_merge($lessonsData, $module->lessons()->pluck('id', 'id')->toArray());
                array_push($moduleData, [
                    'module_id' => $module->id,
                    'status' => $index === 0 ? 'in_progress' : 'inactive',
                    'user_id' => $auth->id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
                array_push($courseModuleTests,  CourseModuleTest::where('course_module_id', $module->id)->pluck('id'));
            }

            foreach ($courseModuleTests as $test) {
                if (count($test)) {
                    array_push($courseModuleTestsData, $dataCourse = [
                        'user_id' => $auth->id,
                        'course_module_test_id' => $test[0],
                        'course_id' => $course->id
                    ]);
                }
            }
            UserCourseModuleTest::insert($courseModuleTestsData);

            UserModule::insert($moduleData);
            if (count($lessonsData)) {
                $auth->lessons()->attach($lessonsData);
                UserLesson::where('lesson_id', $lessonsData[0])->where('user_id', $auth->id)->update(['status' => 'in_progress']);
            }

                foreach ($lessonsData as $lesson_id) {
                    $lesson_block_id = LessonBlock::where('lesson_id', $lesson_id)->pluck('id');
                    $course_module_id = Lesson::where('id', $lesson_id)->pluck('course_module_id')->first();
                    foreach ($lesson_block_id as $block_id) {
                        array_push($lessonBlockData, [
                            'point' => '0',
                            'lesson_block_id' => $block_id,
                            'lesson_id' => $lesson_id,
                            'user_id' => $auth->id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]);
                        $tasks = Task::where('lesson_block_id', $block_id)->pluck('id');
                        foreach ($tasks as $task) {
                            if ($task !== null) {
                                array_push($taskData, [
                                    'task_id' => $task,
                                    'module_id' => $course_module_id,
                                    'status' => 'in_progress',
                                    'point' => '0',
                                    'lesson_id' => $lesson_id,
                                    'user_id' => $auth->id,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now()
                                ]);
                            }
                        }
                    }
                }
              UserTask::insert($taskData);
              UserLessonBlock::insert($lessonBlockData);
            if ($course->course_chat) {
                $course->course_chat->users()->attach(auth('api')->id());
            }

            DB::commit();

            PaymentHistory::create([
                'price' => $tariff->price,
                'status' => 1,
                'payment_method_id' => $request->payment_method_id,
                'course_id' => $data['course_id'],
                'course_tariff_id' => $data['course_tariff_id'],
                'user_id' => $auth->id
            ]);

            $auth->notify(new PaymentNotification($course, 'course_payed'));

            return response()->json([
                'data' => new CourseResource($user_course->load('course')->course)
            ], 200);
        } catch (\Exception $e) {
            DB::rollback();
            $auth->notify(new PaymentNotification($course, 'course_not_payed'));

            PaymentHistory::create([
                'price' => $tariff->price,
                'status' => 0,
                'payment_method_id' => $request->payment_method_id,
                'course_id' => $data['course_id'],
                'course_tariff_id' => $data['course_tariff_id'],
                'user_id' => $auth->id
            ]);
            return response()->json([
                'message' => $e->getMessage()
            ], 200);
        }

    }

    public function paymentHistory (Request $request)
    {
        $payment_history = PaymentHistory::with('payment_method', 'course', 'course_tariff')->orderBy('id', "desc")->where('user_id', auth('api')->id());
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 15;
        $count = $payment_history->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $payment_history = $payment_history->take($take)->skip($skip);
        } else {
            $payment_history = $payment_history->take($take)->skip(0);
        }

        return response()->json([
            'data' => $payment_history->get(),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }
}
