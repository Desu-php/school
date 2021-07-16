<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseLevelResource;
use App\Models\CourseLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseLevelController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {

        $courseLevel = CourseLevel::withTrashed()->orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $courseLevel->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $courseLevel = $courseLevel->take($take)->skip($skip);
        } else {
            $courseLevel = $courseLevel->take($take)->skip(0);
        }

        return response()->json([
            'data' => CourseLevelResource::collection($courseLevel->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $request->all();

        $courseLevel = CourseLevel::create($data);

        return response()->json([
            'data' => $courseLevel
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $courseLevel = CourseLevel::find($id);

        return response()->json([
            'data' => $courseLevel
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();

        $courseLevel = CourseLevel::find($id);
        $courseLevel->name = $data['name'];
        $courseLevel->save();

        return response()->json([
            'data' => $courseLevel
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function courseLevelList(Request $request)
    {
        $courseLevel = CourseLevel::orderBy('id', "desc");

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $courseLevel->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $courseLevel = $courseLevel->take($take)->skip($skip);
        } else {
            $courseLevel = $courseLevel->take($take)->skip(0);
        }

        return response()->json([
            'data' => CourseLevelResource::collection($courseLevel->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {

        $courseLevel = CourseLevel::withTrashed()->findOrFail($id);

        $deleteType = null;

        if(!$courseLevel->trashed()){
            $courseLevel->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $courseLevel->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Course Level has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $task = CourseLevel::withTrashed()->findOrFail($id);

        $task->restore();

        return response()->json([
            'status'   => true,
            'data' => new CourseLevelResource($task),
            'message'  => 'Course Level has been restored successfully!'
        ], 200);
    }
}
