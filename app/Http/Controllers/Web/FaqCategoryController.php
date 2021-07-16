<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\FaqCategoryRequest;
use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

class FaqCategoryController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $faq_categories = FaqCategory::orderBy('sort', "asc")->whereHas('faqs', function ($query) {
            $query->where('faq_id', '>', 0);
        });

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $faq_categories->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $faq_categories = $faq_categories->take($take)->skip($skip);
        } else {
            $faq_categories = $faq_categories->take($take)->skip(0);
        }

        return response()->json([
            'data' => FaqCategoryResource::collection($faq_categories->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

}
