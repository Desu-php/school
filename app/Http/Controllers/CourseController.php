<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseResource;
use App\Jobs\SendNewsLetterMessage;
use App\Models\Course;
use App\Models\CourseChat;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

class CourseController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getList(Request $request)
    {
        $courses = Course::orderBy('id', "desc")->get();


        return response()->json([
            'data' => CourseResource::collection($courses)
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $courses = Course::withTrashed()->orderBy('id', "desc")->with('course_level');

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
     * @return CourseResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'tariffs' => 'required',
            'teaching_language_id' => 'required|exists:teaching_languages,id',
            'course_level_id' => 'required|exists:course_levels,id',
            'announcement_id' => 'required|exists:announcements,id',
            'is_free' => 'required',
            'is_free_lesson' => 'required',
            'lesson_id' => 'nullable',
            'course_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'description',
            'teaching_language_id',
            'course_level_id',
            'announcement_id',
            'course_type',
            'is_free',
            'is_free_lesson'
        );

        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/course/images';
            $file->storeAs($destinationPath, $imageName);
            $data['image'] = $imageName;
        }

        $course = Course::create($data);

        SendNewsLetterMessage::dispatch();

        CourseChat::create([
            'name' => $course->name,
            'status' => 'accepted',
            'course_id' => $course->id,
        ]);

        $course->tariffs()->sync($request->tariffs);

        return new CourseResource($course);
    }

    /**
     * @param $id
     * @return CourseResource
     */
    public function show($id)
    {

        $course = Course::withTrashed()->with('users')->findOrFail($id);

        return new CourseResource($course);
    }

    /**
     * @param Request $request
     * @param $id
     * @return CourseResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'is_free' => 'required',
            'tariffs' => 'required',
            'teaching_language_id' => 'required|exists:teaching_languages,id',
            'course_level_id' => 'required|exists:course_levels,id',
            'announcement_id' => 'required|exists:announcements,id',
            'course_type' => 'required',
            'is_free_lesson' => 'required',
            'lesson_id' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'description',
            'teaching_language_id',
            'course_level_id',
            'announcement_id',
            'is_free',
            'course_type',
            'is_free_lesson'
        );

        $updateFreeLesson = Course::withTrashed()->where('id', $id)->with('course_modules', 'course_modules.lessons')->first();

        if ($updateFreeLesson->course_modules->count()) {
            if (count($updateFreeLesson->course_modules[0]->lessons)){
                if ($request->is_free_lesson) {
                    $lesson = $updateFreeLesson->course_modules[0]->lessons[0];
                    $lessonFree['is_free'] = 1;
                    Lesson::where('id', $lesson->id)->update($lessonFree);
                    $freeLessonId['lesson_id'] = $lesson->id;
                    Course::where('id', $id)->update($freeLessonId);
                } else {
                    $lesson = $updateFreeLesson->course_modules[0]->lessons[0];
                    $lessonFree['is_free'] = 0;
                    Lesson::where('id', $lesson->id)->update($lessonFree);
                    $freeLessonId['lesson_id'] = null;
                    Course::where('id', $id)->update($freeLessonId);
                }
            } else {
                if (!$request->is_free_lesson) {
                    $freeLessonId['is_free_lesson'] = 0;
                    $freeLessonId['lesson_id'] = null;
                    Course::where('id', $id)->update($freeLessonId);
                } else {
                    $freeLessonId['is_free_lesson'] = 1;
                    $freeLessonId['lesson_id'] = null;
                    Course::where('id', $id)->update($freeLessonId);
                }
            }
        }

        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/course/images';
            $file->storeAs($destinationPath, $imageName);
            $data['image'] = $imageName;
        }

        Course::withTrashed()->where('id', $id)->update($data);
        $course = Course::withTrashed()->findOrFail($id);

        $course->tariffs()->sync($request->tariffs);
        return new CourseResource($course);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $course = Course::withTrashed()->findOrFail($id);

        $course->restore();

        return response()->json([
            'status'   => true,
            'data' => new CourseResource($course),
            'message'  => 'Course has been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $course = Course::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$course->trashed()){
            $course->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $course->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Course has been deleted successfully!'
        ], 200);
    }
}
