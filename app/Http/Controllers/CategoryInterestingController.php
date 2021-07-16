<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryInterestingResource;
use App\Models\CategoryInteresting;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CategoryInterestingController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $categories_interesting = CategoryInteresting::withTrashed()->orderBy('sort', "asc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 8;
        $count = $categories_interesting->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $categories_interesting = $categories_interesting->take($take)->skip($skip);
        } else {
            $categories_interesting = $categories_interesting->take($take)->skip(0);
        }

        return response()->json([
            'data' => CategoryInterestingResource::collection($categories_interesting->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     *
     * @return CategoryInterestingResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'color' => 'required',
            'seo_title' => 'required',
            'seo_description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'title',
            'color',
            'seo_title',
            'seo_description'
        );

        if (isset($data['title']['en'])) {
            $slugTitle = $data['title']['en'];
        } else {
            $first_key = key($data['title']);
            $slugTitle = $data['title'][$first_key];
        }

        $data['slug'] = Str::slug($slugTitle, '-');

        $category_interesting = CategoryInteresting::create($data);

        return new CategoryInterestingResource($category_interesting);

    }

    /**
     * Display the specified resource.
     * @param $id
     *
     * @return CategoryInterestingResource
     */
    public function show($id)
    {
        $category_interesting = CategoryInteresting::withTrashed()->findOrFail($id);

        return new CategoryInterestingResource($category_interesting);
    }


    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param         $id
     *
     * @return CategoryInterestingResource|\Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'color' => 'required',
            'seo_title' => 'required',
            'seo_description' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'title',
            'color',
            'seo_title',
            'seo_description'
        );

        if (isset($data['title']['en'])) {
            $slugTitle = $data['title']['en'];
        } else {
            $first_key = key($data['title']);
            $slugTitle = $data['title'][$first_key];
        }

        $data['slug'] = Str::slug($slugTitle, '-');

        CategoryInteresting::withTrashed()->where('id', $id)->update($data);

        return $this->show($id);
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSort(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data.*.sort' => 'required',
            'data.*.id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        foreach ($request->data as $data) {
            CategoryInteresting::where('id', $data['id'])->update(['sort' => $data['sort']]);
        }

        return response()->json([
            'data' => 'Category successfully updated!',
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $category_interesting = CategoryInteresting::withTrashed()->findOrFail($id);

        $category_interesting->restore();

        return response()->json([
            'status'   => true,
            'data' => new CategoryInterestingResource($category_interesting),
            'message'  => 'Category been restored successfully!'
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
        $category_interesting = CategoryInteresting::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$category_interesting->trashed()){
            $category_interesting->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $category_interesting->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Category has been deleted successfully!'
        ], 200);
    }
}
