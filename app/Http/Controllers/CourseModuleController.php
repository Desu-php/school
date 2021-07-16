<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseModuleResource;
use App\Http\Resources\LanguageResource;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Language;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseModuleController extends Controller
{


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseModulesByCourse($id)
    {
        $course_modules = CourseModule::withTrashed()->where('course_id',$id)->get();

        return response()->json([
            'data' => CourseModuleResource::collection($course_modules)
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $course_modules = CourseModule::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $course_modules->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $course_modules = $course_modules->take($take)->skip($skip);
        } else {
            $course_modules = $course_modules->take($take)->skip(0);
        }

        return response()->json([
            'data' => CourseModuleResource::collection($course_modules->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'course_id' => 'required|exists:courses,id',
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course_module = CourseModule::create($validator->validate());

        return response()->json([
            'data' => new CourseModuleResource($course_module)
        ], 200);
    }

    /**
     * @param $id
     * @return CourseModuleResource
     */
    public function show($id)
    {
        $course_module = CourseModule::withTrashed()->findOrFail($id);

        return  new CourseModuleResource($course_module);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'course_id' => 'required|exists:courses,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course_module = CourseModule::withTrashed()->findOrFail($id);
        $course_module->name = $request->name;
        $course_module->description = $request->description;
        $course_module->course_id = $request->course_id;
        $course_module->save();

        return response()->json([
            'data' => new CourseModuleResource($course_module)
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $course_module = CourseModule::withTrashed()->find($id);
        $course_module->restore();

        return response()->json([
            'status'   => true,
            'message'  => 'Course Module has been restored successfully!'
        ], 200);
    }


    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $course_module = CourseModule::withTrashed()->find($id);
        $deleteType = null;

        if(!$course_module->trashed()){
            $course_module->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $course_module->forceDelete();
        }

        $this->updateFreeLesson($course_module->course, $id);

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Course Module has been deleted successfully!'
        ], 200);
    }

    public function updateFreeLesson ($course, $moduleId) {
        $updateFreeLesson = Course::withTrashed()->where('id', $course->id)->with('course_modules', 'course_modules.lessons')->first();
        $courseModules = CourseModule::where('course_id', $course->id)->get();
        $lesson =  Lesson::where('id', $course->lesson_id)->first();

        if ($lesson->course_module_id === +$moduleId) {
            if (count($courseModules)){
                if ($course->is_free_lesson) {
                    $lesson = $updateFreeLesson->course_modules[0]->lessons[0];
                    $lessonFree['is_free'] = 1;
                    Lesson::where('id', $lesson->id)->update($lessonFree);
                    $freeLessonId['lesson_id'] = $lesson->id;
                    Course::where('id', $course->id)->update($freeLessonId);
                }
            } else {
                if ($course->is_free_lesson) {
                    $freeLessonId['lesson_id'] = null;
                    $freeLessonId['is_free_lesson'] = 0;
                    Course::where('id', $course->id)->update($freeLessonId);
                }
            }
        }
    }
}
