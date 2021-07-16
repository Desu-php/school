<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseVideoResource;
use Image;
use App\Models\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseVideoController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseVideosListByCourse($id)
    {
        $video_course = CourseVideo::withTrashed()->where('course_id',$id)->get();

        return response()->json([
            'data' => CourseVideoResource::collection($video_course)
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
            'image' => 'required|image',
            'video_iframe' => 'sometimes',
            'video_type' => 'required',
            'video' => 'sometimes|mimes:mp4,webm,ogg'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'description',
            'course_id',
            'video_iframe',
            'video'
        );

        if ($request->video_type === 'video' && $request->has('video')) {
            $video = $request->video;
            $data['video'] = $this->videoUpload($video);
            $data['video_iframe'] = null;
        }

        if ($request->video_type === 'video_iframe' && $request->has('video_iframe')) {
            $data['video'] = null;
        }


        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/course/video-image';
            $file->storeAs($destinationPath, $imageName);
            $data['image'] = $imageName;
        }

        $video_course = CourseVideo::create($data);

        return response()->json([
            'data' => $video_course
        ], 200);
    }

    /**
     * @param $id
     * @return CourseVideoResource
     */
    public function show($id)
    {
        $video_course = CourseVideo::withTrashed()->findOrFail($id);

        return  new CourseVideoResource($video_course);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update (Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
            'course_id' => 'required|exists:courses,id',
            'image' => 'sometimes|image',
            'video_iframe' => 'sometimes',
            'video_type' => 'required',
            'video' => 'sometimes|mimes:mp4,webm,ogg'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'description',
            'course_id',
            'video_iframe',
            'video'
        );

        if ($request->video_type === 'video' && $request->has('video')) {
            $video = $request->video;
            $data['video'] = $this->videoUpload($video);
            $data['video_iframe'] = null;
        }

        if ($request->video_type === 'video_iframe' && $request->has('video_iframe')) {
            $data['video'] = null;
        }

        if ($request->has('image') && $request->image) {
            $file = $request->image;
            $imageName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/course/video-image';
            $file->storeAs($destinationPath, $imageName);
            $data['image'] = $imageName;
        }

        CourseVideo::withTrashed()->where('id', $id)->update($data);

        $video_course = CourseVideo::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new CourseVideoResource($video_course)
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $video_course = CourseVideo::withTrashed()->find($id);
        $video_course ->restore();

        return response()->json([
            'status'   => true,
            'message'  => 'Course Video has been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $video_course = CourseVideo::withTrashed()->find($id);
        $deleteType = null;

        if(!$video_course->trashed()){
            $video_course->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $video_course->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Course Video has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $file
     * @return string
     */
    public function videoUpload($file)
    {
        $videoName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/course/video';
        $file->storeAs($destinationPath, $videoName);

        return $videoName;
    }
}
