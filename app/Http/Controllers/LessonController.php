<?php

namespace App\Http\Controllers;

use App\Http\Resources\LessonResource;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LessonController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $lessons = Lesson::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $lessons->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $lessons = $lessons->take($take)->skip($skip);
        } else {
            $lessons = $lessons->take($take)->skip(0);
        }

        return response()->json([
            'data' => LessonResource::collection($lessons->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLessonsByModule($id)
    {
        $lessons = Lesson::withTrashed()->where('course_module_id',$id)->get();

        return response()->json([
            'data' => LessonResource::collection($lessons)
        ], 200);
    }

    /**
     * @param Request $request
     * @return LessonResource|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'course_module_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only( 'name', 'description', 'short_description', 'video_iframe', 'course_module_id');

        if ($request->has('video_file') && $request->video_file) {
            $file = $request->file('video_file');
            $fileName = time().'-'.uniqid().'.'. $file->extension();
            $destinationPath = 'public/lesson/videos';
            $file->storeAs($destinationPath, $fileName);
            $data['video_file'] = $fileName;
        }

        $bool = true;
        $modul = CourseModule::whereHas('course', function ($query) {
            if ($query) {
                $query->where('is_free_lesson', 1);
            }
        })->where('id', $request->course_module_id);
            if (count($modul->get())) {
                $cours_id = $modul->first()->course->id;
                $mod = CourseModule::where('course_id', $cours_id)->orderBy('created_at', 'asc')->first()->id;
                if ($mod == $request->course_module_id) {
                    $les = Lesson::where('course_module_id', $request->course_module_id)->get();
                    if (!count($les)) {
                        $data['is_free'] = 1;
                        $lesson = Lesson::create($data);
                        $dat = [
                            'lesson_id' => $lesson->id
                        ];
                        Course::where('id', $cours_id)->update($dat);
                        $bool = false;
                    }
                }
            }

        if ($bool) {
            $data['is_free'] = 0;
            $lesson = Lesson::create($data);
        }
        return new LessonResource($lesson);
    }

    /**
     * @param $id
     * @return LessonResource
     */
    public function show($id)
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);
        return new LessonResource($lesson);
    }

    /**
     * @param Request $request
     * @param $id
     * @return LessonResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'        => 'required',
            'description' => 'required',
            'short_description' => 'required',
            'course_module_id' => 'required|exists:course_modules,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only( 'name', 'description', 'short_description', 'video_iframe', 'course_module_id' );

        if ($request->has('video_file') && $request->video_file) {
            $file = $request->file('video_file');
            $fileName = time().'-'.uniqid().'.'. $file->extension();
            $destinationPath = 'public/lesson/videos';
            $file->storeAs($destinationPath, $fileName);
            $data['video_file'] = $fileName;
        }

        $lesson = Lesson::where('id', $id)->update($data);

        return $this->show($id);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);

        $lesson->restore();

        return response()->json([
            'status'   => true,
            'data' => new LessonResource($lesson),
            'message'  => 'Lesson has been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$lesson->trashed()){
            $lesson->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $lesson->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Lesson has been deleted successfully!'
        ], 200);
    }
}
