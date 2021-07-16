<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\Web\LessonResource;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use App\Models\UserLesson;
use App\Models\UserModule;
use App\Models\UserTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatisticsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statisticsCourse(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course = Course::where('id', $request->course_id)->first();
        $course_module = CourseModule::where('course_id', $request->course_id)->pluck('id');


        $module_point = UserModule::whereIn('module_id', $course_module)->where('user_id', auth('api')->id())->pluck('point')->all();
        $module_point_count = UserModule::whereIn('module_id', $course_module)->where('user_id', auth('api')->id())->count();
         if(count($module_point)) {
             $course_point = array_sum($module_point) / $module_point_count;
         } else {
             $course_point = 0;
         }
        $module_point_done = UserModule::whereIn('module_id', $course_module)->where('status', 'done')->where('user_id', auth('api')->id())->count();
        $lessons_id = Lesson::whereIn('course_module_id', $course_module)->pluck('id');
        $lessons_point =  $lessons_id->count();
        $lessons_point_done = UserLesson::whereIn('lesson_id', $lessons_id)->where('status', 'done')->where('user_id', auth('api')->id())->count();
        $task_point = UserTask::whereIn('module_id', $course_module)->where('user_id', auth('api')->id())->count();
        $task_point_done = UserTask::whereIn('module_id', $course_module)->where('status', 'done')->where('user_id', auth('api')->id())->count();


        $data = [
            'course' => $course,
            'course_point' => $course_point,
            'module_point' => $module_point_count,
            'module_point_done' => $module_point_done,
            'task_point' => $task_point,
            'task_point_done' => $task_point_done,
            'lessons_point' => $lessons_point,
            'lessons_point_done' => $lessons_point_done
        ];

        return response()->json([
           'data' => $data
        ], 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function statisticsLessons (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course_module = CourseModule::where('course_id', $request->course_id)->pluck('id');
        $lessons = Lesson::whereIn('course_module_id', $course_module)->get();
//        $lessons_id = Lesson::whereIn('course_module_id', $course_module)->pluck('id');
        return response()->json([
            'data' => LessonResource::collection($lessons),


        ], 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function statisticsTasks (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lessons_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $lessons = Lesson::where('id', $request->lessons_id)->first();

        return response()->json([
            'data' => new LessonResource($lessons)
        ], 200);
    }
}
