<?php

namespace App\Http\Controllers;

use App\Http\Resources\LanguageResource;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use function React\Promise\Stream\first;

class LanguageController extends Controller
{
  /**
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   */
  public function getAll()
  {

    return LanguageResource::collection(Language::withTrashed()->get());
  }
  /**
   * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
   */
  public function index()
  {

    return LanguageResource::collection(Language::all());
  }


  /**
   * @param Request $request
   *
   * @return \Illuminate\Http\JsonResponse
   * @throws \Illuminate\Validation\ValidationException
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'code' => 'required',
      'name' => 'required',
    ]);

    if ($validator->fails()) {
      return response()->json($validator->errors(), 422);
    }

      $data = $request->all(
          'code',
          'name',
          'localization_json'
      );

    $language = Language::create($data);

    return response()->json([
      'data' => new LanguageResource($language)
    ], 200);
  }

  /**
   * @param $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function show($id)
  {
    $language = Language::findOrFail($id);

    return response()->json([
      'data' => new LanguageResource($language)
    ], 200);
  }


  /**
   * @param Request $request
   * @param         $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(Request $request, $id)
  {

    $validator = Validator::make($request->all(), [
      'code' => 'required',
      'name' => 'required',
    ]);

    $data = $request->all(
        'code',
        'name',
        'localization_json'
    );

    Language::where('id', $id)->update($data);

    return response()->json([
      'data' => new LanguageResource(Language::find($id))
    ], 200);
  }

  /**
   * @param $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy($id)
  {
    $language = Language::withTrashed()->find($id);
    $deleteType = null;
    if(!$language->trashed()){
      $language->delete();
      $deleteType = 'delete';
    }
    else {
      $deleteType = 'forceDelete';
      $language->forceDelete();
    }

    return response()->json([
      'status'   => true,
      'deleteType' => $deleteType,
      'message'  => 'Language has been deleted successfully!'
    ], 200);
  }

  /**
   * @param $id
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function restore($id)
  {

    $language = Language::withTrashed()->find($id);

    $language->restore();

    return response()->json([
      'status'   => true,
      'language' => new LanguageResource($language),
      'message'  => 'Language has been restored successfully!'
    ], 200);
  }

  public function getLocalizationJson($code)
  {
      $language = Language::where('code', $code)->first();

      return response()->json([
          'data'  => $language->localization_json
      ], 200);
  }
}
