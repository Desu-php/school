<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryInterestingResource;
use App\Models\CategoryInteresting;
use Illuminate\Http\Request;

class CategoryInterestingController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $categories_interesting = CategoryInteresting::withTrashed()->orderBy('sort', "asc")->get();

        return CategoryInterestingResource::collection($categories_interesting);
    }
}
