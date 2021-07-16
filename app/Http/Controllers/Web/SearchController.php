<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Interesting;
use App\Models\News;
use Illuminate\Http\Request;

class SearchController extends Controller
{

    public function index(Request $request)
    {
        if($request->value === null) {
            return response()->json([
                'error' => 'value_empty',
                'message' => 'Search value is empty!',
            ], 200);
        }

        $courses = Course::orderBy('id', 'desc')->select('description','id','name')->where('name', 'LIKE' , '%'.$request->value.'%')->get()->toArray();
        $interestings = Interesting::orderBy('id', 'desc')->where('title', 'like' , '%'.$request->value.'%')->select('short_description','id','title')->get()->toArray();
        $news = News::orderBy('id', 'desc')->where('title', 'like' , '%'.$request->value.'%')->select('short_description','id','title')->get()->toArray();

        $courseArr = array_merge($courses,$interestings,$news);
        $collectIon =  collect($courseArr)->sortBy('id');
        $page = $request->input('page') ?: 1;
        $take = $request->input('count') ?: 4;
        $count = $collectIon->count();

        if ($page) {
            $skip = $take * ((int)$page - 1);
            $collectIon = $collectIon->skip($skip)->take($take);
        } else {
            $collectIon = $collectIon->take($take)->skip(0);
        }

        return response()->json([
            'data' => $collectIon,
            'pagination' => [
               'count_pages' => ceil($count / $take),
               'count' => $count
           ]
        ], 200);

    }

}
