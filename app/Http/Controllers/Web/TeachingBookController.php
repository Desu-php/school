<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\TeachingBookResource;
use App\Models\TeachingBook;
use Illuminate\Http\Request;

class TeachingBookController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $teaching_books = TeachingBook::orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $teaching_books->count();


        if ($page) {
            $skip = $take * ($page - 1);
            $teaching_books = $teaching_books->take($take)->skip($skip);
        } else {
            $teaching_books = $teaching_books->take($take)->skip(0);
        }

        return response()->json([
            'data' => TeachingBookResource::collection($teaching_books->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }
}
