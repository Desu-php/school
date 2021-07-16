<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\DynamicPageResource;
use App\Http\Resources\DynamicPageTextResource;
use App\Models\DynamicPage;
use App\Models\DynamicPageText;
use Illuminate\Http\Request;

class DynamicPageController extends Controller
{
    /**
     * @param $id
     * @return DynamicPageResource
     */
    public function show($id)
    {
        $dynamic_page = DynamicPage::findOrFail($id);

        return new DynamicPageResource($dynamic_page);
    }

    /**
     * @param $key
     * @return DynamicPageResource|\Illuminate\Http\JsonResponse
     */
    public function getPageTextByKey($key)
    {
        $dynamic_page = DynamicPage::where('key', $key)->with(['dynamic_page_texts' => function($query) {
                $query->where('is_current', true);
            }])->first();

        if ($dynamic_page) {
            return new DynamicPageResource($dynamic_page);
        }

        return response()->json([
            'data' => null
        ]);
    }

}
