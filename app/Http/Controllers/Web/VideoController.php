<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $video = Video::orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ?: 10;

        $count = $video->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $video = $video->take($take)->skip($skip);
        } else {
            $video = $video->take($take)->skip(0);
        }

        return response()->json([
            'data' =>  VideoResource::collection($video->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }
}
