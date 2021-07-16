<?php

namespace App\Http\Controllers;

use App\Http\Resources\VideoResource;
use App\Models\News;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

class VideoController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $videos = Video::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ?: 1;
        $take = $request->input('count') ?: 6;
        $count = $videos->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $videos = $videos->take($take)->skip($skip);
        } else {
            $videos = $videos->take($take)->skip(0);
        }

        return response()->json([
            'data'       => VideoResource::collection($videos->get()),
            'pagination' => [
                'count_pages' => ceil($count / $take),
                'count'       => $count
            ]
        ], 200);
    }


    /**
     * @param Request $request
     *
     * @return VideoResource
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
            $destinationPath = storage_path('app/public/video/gallery');
            $this->imageUpload($destinationPath, $image, $imageName);
            $data['image'] = $imageName;
            $data['image_extension'] = $image->extension();;
        }

        $video = Video::create($data);
        return new VideoResource($video);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $video = Video::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new VideoResource($video)
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return VideoResource
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required',
            'description' => 'required',
            'image_alt' => 'required',
            'video_iframe'     => 'sometimes',
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
            $imageName = time().'-'.uniqid();
            $destinationPath = storage_path('app/public/video/gallery');
            $this->imageUpload($destinationPath, $image, $imageName);
            $data['image'] = $imageName;
            $data['image_extension'] = $image->extension();;
        }

        Video::withTrashed()->where('id', $id)->update($data);

        return new VideoResource(Video::findOrFail($id));
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $video = Video::withTrashed()->findOrFail($id);

        $video->restore();

        return response()->json([
            'status'   => true,
            'data' => new VideoResource($video),
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
        $video = Video::withTrashed()->findOrFail($id);

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

    /**
     * @param $file
     *
     * @return mixed
     */
    public function videoUpload($file)
    {
        $videoName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/video/videos';
        $file->storeAs($destinationPath, $videoName);

        return $videoName;
    }
}
