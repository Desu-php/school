<?php

namespace App\Http\Controllers;

use App\Http\Resources\DynamicPageTextResource;
use App\Models\DynamicPageText;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DynamicPageTextController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $dynamic_page_texts = DynamicPageText::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $dynamic_page_texts->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $dynamic_page_texts = $dynamic_page_texts->take($take)->skip($skip);
        } else {
            $dynamic_page_texts = $dynamic_page_texts->take($take)->skip(0);
        }

        return response()->json([
            'data' => DynamicPageTextResource::collection($dynamic_page_texts->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return DynamicPageTextResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description'       => 'required',
            'dynamic_page_id' => 'required|exists:dynamic_pages,id',
        ]);

        $dynamic_page_text = DynamicPageText::create($validator->validate());

        return new DynamicPageTextResource($dynamic_page_text);
    }

    /**
     * @param $id
     *
     * @return DynamicPageTextResource
     */
    public function show($id)
    {
        $dynamic_page_text = DynamicPageText::withTrashed()->find($id);

        return new DynamicPageTextResource($dynamic_page_text);
    }


    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function setCurrent($id)
    {
        DynamicPageText::where('id', $id)->update([
            'is_current' => true
        ]);

        DynamicPageText::where('id', '!=', $id)->update([
            'is_current' => false
        ]);

        return response()->json([
            'status'   => true,
            'message'  => 'Current page text set successfully!'
        ], 200);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'description'       => 'required',
            'dynamic_page_id' => 'required|exists:dynamic_pages,id',
        ]);

        DynamicPageText::withTrashed()->where('id', $id)->update($validator->validate());
        $dynamic_page_text = DynamicPageText::withTrashed()->find($id);

        return response()->json([
            'data' => new DynamicPageTextResource($dynamic_page_text)
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $dynamic_page_text = DynamicPageText::withTrashed()->findOrFail($id);

        $dynamic_page_text->restore();

        return response()->json([
            'status'   => true,
            'data' => new DynamicPageTextResource($dynamic_page_text),
            'message'  => 'Page text has been restored successfully!'
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
        $dynamic_page_text = DynamicPageText::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$dynamic_page_text->trashed()){
            $dynamic_page_text->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $dynamic_page_text->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Page text has been deleted successfully!'
        ], 200);
    }
}
