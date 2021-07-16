<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeachingAudioResource;
use App\Models\TeachingAudio;
use Illuminate\Http\Request;

class TeachingAudioController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $teaching_videos = TeachingAudio::orderBy('id', "desc");

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

}
