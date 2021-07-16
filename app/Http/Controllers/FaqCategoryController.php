<?php

namespace App\Http\Controllers;

use App\Http\Requests\FaqCategoryRequest;
use App\Http\Resources\FaqCategoryResource;
use App\Models\FaqCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqCategoryController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request)
    {
        $faq_categories = FaqCategory::withTrashed()->orderBy('sort', "asc");
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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $faq_categories = FaqCategory::orderBy('sort', "asc");
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

  /**
   * @param FaqCategoryRequest $request
   *
   * @return FaqCategoryResource
   * @throws \Illuminate\Validation\ValidationException
   */
    public function store(FaqCategoryRequest $request)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
      ]);

      $faq_category = FaqCategory::create($validator->validate());

      return new FaqCategoryResource($faq_category);
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
            FaqCategory::where('id', $data['id'])->update(['sort' => $data['sort']]);
        }

        return response()->json([
            'data' => 'FaqCategory successfully updated!',
        ], 200);
    }
  /**
   * @param $id
   *
   * @return FaqCategoryResource
   */
    public function show($id)
    {
      $faq_category = FaqCategory::findOrFail($id);
      return new FaqCategoryResource($faq_category);
    }

  /**
   * @param Request $request
   * @param         $id
   *
   * @return FaqCategoryResource
   * @throws \Illuminate\Validation\ValidationException
   */
    public function update(Request $request, $id)
    {
      $validator = Validator::make($request->all(), [
        'name' => 'required',
      ]);


      $faq_category = FaqCategory::where('id', $id)->update($validator->validate());

      if ($faq_category) {
        return new FaqCategoryResource(FaqCategory::find($id));
      }
    }

  /**
   * @param $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function restore($id)
  {

    $faq_category = FaqCategory::withTrashed()->find($id);

    $faq_category->restore();

    return response()->json([
      'status'   => true,
      'faq_category' => new FaqCategoryResource($faq_category),
      'message'  => 'Faq Category has been restored successfully!'
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

      $faq_category = FaqCategory::withTrashed()->find($id);

      $deleteType = null;

      if(!$faq_category->trashed()){
        $faq_category->delete();
        $deleteType = 'delete';
      }
      else {
        $deleteType = 'forceDelete';
        $faq_category->forceDelete();
      }

      return response()->json([
        'status' => true,
        'deleteType' => $deleteType,
        'message' => 'Faq Category has been deleted successfully!'
      ], 200);
    }
}
