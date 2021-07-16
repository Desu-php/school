<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Http\Resources\TestResource;
use App\Models\CourseTest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CourseTestController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseTestsListByCourse($id)
    {
        $test_course = CourseTest::withTrashed()->where('course_id',$id)->get();

        return response()->json([
            'data' => TestResource::collection($test_course)
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTestTaskList($id)
    {
        $test_tasks = Task::withTrashed()->where('test_id',$id)->get();

        return response()->json([
            'data' => TaskResource::collection($test_tasks)
        ], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'course_id' => 'required|exists:courses,id'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $test_course = CourseTest::create($validator->validate());

        return response()->json([
            'data' => $test_course
        ], 200);
    }

    /**
     * @param $id
     * @return TestResource
     */
    public function show($id)
    {
        $test_course = CourseTest::withTrashed()->findOrFail($id);

        return  new TestResource($test_course);
    }

    /**
     * @param $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update (Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'course_id' => 'required|exists:courses,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        CourseTest::withTrashed()->where('id', $id)->update($validator->validate());

        $test_course = CourseTest::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new TestResource($test_course)
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $test_course = CourseTest::withTrashed()->find($id);
        $test_course ->restore();

        return response()->json([
            'status'   => true,
            'message'  => 'Course Test has been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $test_course = CourseTest::withTrashed()->find($id);
        $deleteType = null;

        if(!$test_course->trashed()){
            $test_course->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $test_course->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Lesson Block has been deleted successfully!'
        ], 200);
    }
}
