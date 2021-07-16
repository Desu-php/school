<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\AvailableCourseResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseVideoResource;
use App\Models\Course;
use App\Models\CourseTariff;
use App\Models\CourseVideo;
use App\Models\UserCourse;
use App\Models\UserModule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $courses = Course::orderBy('id', 'desc')->where('course_type', $request->input('course_type'));

        if ($request->has('language') &&  $request->input('language') !== 'all') {
            $courses = $courses->where('teaching_language_id', $request->input('language'));
        }
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;

        $count = $courses->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $courses = $courses->take($take)->skip($skip);
        } else {
            $courses = $courses->take($take)->skip(0);
        }

        return response()->json([
            'data' => CourseResource::collection($courses->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function availableCourse(Request $request)
    {
        $courses = Course::orderBy('id', 'desc')->where('course_type', $request->input('course_type'));

        if ($request->has('language') &&  $request->input('language') !== 'all') {
            $courses = $courses->where('teaching_language_id', $request->input('language'));
        }
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;

        $count = $courses->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $courses = $courses->take($take)->skip($skip);
        } else {
            $courses = $courses->take($take)->skip(0);
        }

        return response()->json([
            'data' => AvailableCourseResource::collection($courses->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function menuCourses()
    {
        $courses = Course::all();

        return response()->json([
            'data' => CourseResource::collection($courses),
        ], 200);
    }


    public function getCourseVideoMaterials (Request $request)
    {
        $course_video = CourseVideo::withTrashed()->where('course_id',$request->id)->get();
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;

        $count = $course_video->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $course_video = $course_video->take($take)->skip($skip);
        } else {
            $course_video = $course_video->take($take)->skip(0);
        }

        return response()->json([
            'data' => CourseVideoResource::collection($course_video),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function boughtCourses()
    {
        $boughtCourses = UserCourse::where('user_id', auth('api')->id())->with('course')->get();

        return response()->json([
            'data' => $boughtCourses,
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriptionCourses()
    {
        $subscriptionCourses = UserCourse::where('user_id', auth('api')->id())->with('course_tariff', 'course', 'course.course_level', 'course.tariffs')->get();

        return response()->json([
            'data' => $subscriptionCourses,
        ], 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subscriptionUpdateExpiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'subscription_id' => 'required|exists:user_courses,id',
            'course_tariff_id' => 'nullable|exists:course_tariffs,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = UserCourse::where('id', $request->subscription_id)->first();

        if ($request->course_tariff_id) {
            $data['course_tariff_id'] = $request->course_tariff_id;
            $tariff = CourseTariff::where('id', $request->course_tariff_id)->first();
            $data['expiry_date'] = Carbon::now()->addDays($tariff->duration);
        } else {
            $tariff = CourseTariff::where('id', $request->payment_method_id)->first();
            $data['expiry_date'] = Carbon::parse($data['expiry_date'])->addDays($tariff->duration);
        }

        $data->save();


        $dataSubscription = UserCourse::where('user_id', auth('api')->id())->with('course_tariff', 'course', 'course.course_level', 'course.tariffs')->get();
        return response()->json([
            'data' => $dataSubscription,
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showBoughtCourseModule($id)
    {
        $buyCourseId = UserCourse::where('user_id', auth('api')->id())->get()->pluck('course_id');

        if (in_array($id, $buyCourseId->all())) {
            $buyCourse = UserCourse::where('user_id', auth('api')->id())->get()->pluck('course');
            $buyCourse = $buyCourse->where('id', $id)->first();

            return response()->json([
                'data' => new CourseResource($buyCourse->load('course_modules')),
            ], 200);

        } else {
            return response()->json([
                'message' => 'Course not found',
            ], 404);
        }
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseTeachingLanguage($id)
    {
       $course = Course::where('id',$id)->first()->teaching_language;
        return response()->json([
            'data' => $course,
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $course = Course::findOrFail($id);

        return response()->json([
            'data' => new CourseResource($course),
        ], 200);
    }
}
