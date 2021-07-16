<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;
use Image;

class NewsController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $news = News::orderBy('id', 'Desc');
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $except_news = $request->input('except_news') ? : null;

        if ($except_news) {
            $news = $news->where('id', '!=', $except_news);
        }

        $count = $news->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $news = $news->take($take)->skip($skip);
        } else {
            $news = $news->take($take)->skip(0);
        }

        return response()->json([
            'data' =>  NewsResource::collection($news->get()),
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
    public function show($id)
    {
      $news = News::findOrFail($id);

      return response()->json([
        'data' => new NewsResource($news)
      ], 200);
    }
}
