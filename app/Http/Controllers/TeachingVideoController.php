<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeachingVideoResource;
use App\Models\News;
use App\Models\TeachingVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

class TeachingVideoController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $teaching_videos = TeachingVideo::withTrashed()->orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $teaching_videos->count();


        if ($page) {
            $skip = $take * ($page - 1);
            $teaching_videos = $teaching_videos->take($take)->skip($skip);
        } else {
            $teaching_videos = $teaching_videos->take($take)->skip(0);
        }

        return response()->json([
            'data' => TeachingVideoResource::collection($teaching_videos->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return TeachingVideoResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'image_alt' => 'required',
            'video_iframe'     => 'sometimes',
            'video'       => 'sometimes|mimes:mp4,webm,ogg',
            'image'       => 'required|image'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->has('video')) {
            $video = $request->video;
            $data['video'] = $this->videoUpload($video);
            $data['video_iframe'] = null;
        }

        if ($request->has('video_iframe')) {
            $data['video'] = null;
        }

        if ($request->has('image')) {
            $image = $request->image;
            $imageName = time() . '-' . uniqid();
            $destinationPath = storage_path('app/public/teaching-videos/images');
            $this->imageUpload($destinationPath, $image, $imageName);
            $data['image'] = $imageName;
            $data['image_extension'] = $image->extension();;
        }

        $video = TeachingVideo::create($data);
        return new TeachingVideoResource($video);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $teaching_video = TeachingVideo::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new TeachingVideoResource($teaching_video)
        ], 200);
    }


    /**
     * @param Request $request
     * @param         $id
     *
     * @return TeachingVideoResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'video_iframe'     => 'sometimes',
            'image_alt'   => 'required',
            'video'       => 'sometimes|mimes:mp4,webm,ogg'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->has('video')) {
            $video = $request->video;
            $data['video'] = $this->videoUpload($video);
            $data['video_iframe'] = null;
        }

        if ($request->has('video_iframe')) {
            $data['video'] = null;
        }

        if ($request->has('image')) {
            $image = $request->image;
            $imageName = time() . '-' . uniqid();
            $destinationPath = storage_path('app/public/teaching-videos/images');
            $this->imageUpload($destinationPath, $image, $imageName);
            $data['image'] = $imageName;
            $data['image_extension'] = $image->extension();;
        }

        $video = TeachingVideo::where('id', $id)->update($data);

        return $this->show($id);
    }
    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $video = TeachingVideo::withTrashed()->findOrFail($id);

        $video->restore();

        return response()->json([
            'status'   => true,
            'data' => new TeachingVideoResource($video),
            'message'  => 'Video has been restored successfully!'
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
        $video = TeachingVideo::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$video->trashed()){
            $video->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $video->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Video has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $destinationPath
     * @param $file
     * @param $name
     */
    public function imageUpload($destinationPath, $file, $name) {
        $img = Image::make($file->path());
        foreach (News::IMAGE_SIZES as $key => $size) {
            $img->resize($size, $size)->save($destinationPath . '/' . $name
                . '_' . $key . '.' . $file->extension());
        }
    }
}
