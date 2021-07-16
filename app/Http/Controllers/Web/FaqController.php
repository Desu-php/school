<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $faq = Faq::with('categories')->orderBy('sort', "desc");

        if ($request->has('category_id') && $request->query('category_id') !== 'All') {
            $faq = Faq::orderBy('sort', "desc")->whereHas('categories',
                function ($query) use ($request) {
                    $query->where('faq_category_id',
                        $request->query('category_id'));
                });
        }
        return FaqResource::collection($faq->get());
    }
}
