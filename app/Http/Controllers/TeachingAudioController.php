<?php

namespace App\Http\Controllers;

use App\Http\Resources\TeachingAudioResource;
use App\Models\TeachingAudio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeachingAudioController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $teaching_videos = TeachingAudio::withTrashed()->orderBy('id', "desc");

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
            'data' => TeachingAudioResource::collection($teaching_videos->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return TeachingAudioResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'description' => 'required',
            'audio' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->has('audio') && $request->audio) {
            $file = $request->audio;
            $audioName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/teaching-audios';
            $file->storeAs($destinationPath, $audioName);
            $data['audio'] = $audioName;
        }


        $audio = TeachingAudio::create($data);

        return new TeachingAudioResource($audio);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $teaching_audio = TeachingAudio::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new TeachingAudioResource($teaching_audio)
        ], 200);
    }


    /**
     * @param Request $request
     * @param         $id
     *
     * @return TeachingAudioResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();

        if ($request->has('audio') && $request->audio) {
            $file = $request->audio;
            $audioName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/teaching-audios';
            $file->storeAs($destinationPath, $audioName);
            $data['audio'] = $audioName;
        }

        TeachingAudio::withTrashed()->where('id', $id)->update($data);

        return $this->show($id);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $teaching_audio = TeachingAudio::withTrashed()->findOrFail($id);

        $teaching_audio->restore();

        return response()->json([
            'status'   => true,
            'data' => new TeachingAudioResource($teaching_audio),
            'message'  => 'Audio been restored successfully!'
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
        $teaching_audio = TeachingAudio::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$teaching_audio->trashed()){
            $teaching_audio->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $teaching_audio->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Audio has been deleted successfully!'
        ], 200);
    }
}
