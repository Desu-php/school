<?php

namespace App\Http\Controllers;

use App\Http\Resources\FaqResource;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function getAll(Request $request)
    {
        $faq = Faq::withTrashed()->with('categories')->orderBy('sort', "asc");
        if ($request->has('category_id') && $request->query('category_id') !== 'All') {
            $faq = $faq->whereHas('categories', function ($query) use ($request) {
                $query->where('faq_category_id', $request->query('category_id'));
            });
        }
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $faq->count();


        if ($page) {
            $skip = $take * ($page - 1);
            $faq = $faq->take($take)->skip($skip);
        } else {
            $faq = $faq->take($take)->skip(0);
        }

        return response()->json([
            'data' => FaqResource::collection($faq->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }
  /**
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   */
    public function index(Request $request)
    {
      $faq = Faq::with('categories')->orderBy('sort', "asc");

      if ($request->has('category_id') && $request->query('category_id') !== 'All') {
        $faq = Faq::orderBy('sort', "asc")->whereHas('categories', function ($query) use ($request) {
          $query->where('faq_category_id', $request->query('category_id'));
        });
      }

      return FaqResource::collection($faq->get());
    }

  /**
   * @param Request $request
   *
   * @return FaqResource|\Illuminate\Http\JsonResponse
   * @throws \Illuminate\Validation\ValidationException
   */
    public function store(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'question'     => 'required',
            'answer'       => 'required',
            'categories'       => 'required',
            'categories.*.id' => 'required|exists:faq_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $category_ids = collect($request->categories)->pluck('id')->all();
        $faq = Faq::create($validator->validate());
        $faq->categories()->attach($category_ids);

        return new FaqResource($faq);
    }

  /**
   * @param Faq $faq
   *
   * @return FaqResource
   */
    public function show(Faq $faq)
    {
      return new FaqResource($faq);
    }

  /**
   * @param Request $request
   * @param Faq     $faq
   *
   * @return FaqResource|\Illuminate\Http\JsonResponse
   * @throws \Illuminate\Validation\ValidationException
   */
    public function update(Request $request, Faq $faq)
    {
        $validator = Validator::make($request->all(), [
          'question' => 'required',
          'answer' => 'required',
          'categories' => 'required',
          'categories.*.id' => 'required|exists:faq_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $category_ids = collect($request->categories)->pluck('id')->all();
        $faq->update($request->all());
        $faq->categories()->sync($category_ids);

        return new FaqResource($faq);
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
            Faq::where('id', $data['id'])->update(['sort' => $data['sort']]);
        }

        return response()->json([
            'data' => 'Faq successfully updated!',
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $faq = Faq::withTrashed()->findOrFail($id);

        $faq->restore();

        return response()->json([
            'status'   => true,
            'faq_category' => new FaqResource($faq),
            'message'  => 'Faq has been restored successfully!'
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
      $faq = Faq::withTrashed()->findOrFail($id);

      $deleteType = null;

      if(!$faq->trashed()){
        $faq->delete();
        $deleteType = 'delete';
      }
      else {
        $deleteType = 'forceDelete';
        $faq->forceDelete();
      }

      return response()->json([
        'status' => true,
        'deleteType' => $deleteType,
        'message' => 'Faq has been deleted successfully!'
      ], 200);
    }
}
