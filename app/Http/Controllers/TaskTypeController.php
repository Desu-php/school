<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskTypeResource;
use App\Models\TaskType;

class TaskTypeController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $taskTypes = TaskType::withTrashed();

        return response()->json([
            'data' => TaskTypeResource::collection($taskTypes->get()),
        ], 200);
    }

    /**
     * @param $id
     * @return TaskTypeResource
     */
    public function show($id)
    {
        $lesson_block = TaskType::withTrashed()->findOrFail($id);

        return  new TaskTypeResource($lesson_block);
    }
}
