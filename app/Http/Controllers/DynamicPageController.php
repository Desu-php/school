<?php

namespace App\Http\Controllers;

use App\Http\Resources\DynamicPageResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\DynamicPage;

class DynamicPageController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $dynamic_pages = DynamicPage::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $dynamic_pages->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $dynamic_pages = $dynamic_pages->take($take)->skip($skip);
        } else {
            $dynamic_pages = $dynamic_pages->take($take)->skip(0);
        }

        return response()->json([
            'data' => DynamicPageResource::collection($dynamic_pages->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $dynamic_pages = DynamicPage::orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $dynamic_pages->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $dynamic_pages = $dynamic_pages->take($take)->skip($skip);
        } else {
            $dynamic_pages = $dynamic_pages->take($take)->skip(0);
        }

        return response()->json([
            'data' => DynamicPageResource::collection($dynamic_pages->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return DynamicPageResource
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'key' => 'required',
        ]);


        $dynamic_page = DynamicPage::create($validator->validate());

        return new DynamicPageResource($dynamic_page);
    }

    /**
     * @param $id
     *
     * @return DynamicPageResource
     */
    public function show($id)
    {
        $dynamic_page = DynamicPage::withTrashed()->findOrFail($id);

        return new DynamicPageResource($dynamic_page);
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
            'title'       => 'required',
            'key'       => 'required',
        ]);

        DynamicPage::withTrashed()->where('id', $id)->update($validator->validate());

        return response()->json([
            'data' => new DynamicPageResource(DynamicPage::find($id))
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {

        $dynamic_page = DynamicPage::withTrashed()->findOrFail($id);

        $dynamic_page->restore();

        return response()->json([
            'status'   => true,
            'data' => new DynamicPageResource($dynamic_page),
            'message'  => 'Page has been restored successfully!'
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
        $dynamic_page = DynamicPage::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$dynamic_page->trashed()){
            $dynamic_page->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $dynamic_page->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Page has been deleted successfully!'
        ], 200);
    }
}
