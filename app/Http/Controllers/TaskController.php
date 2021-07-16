<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTasksByLessonBlock($id)
    {
        $tasks = Task::withTrashed()->where('lesson_block_id',$id)->get();

        return response()->json([
            'data' => TaskResource::collection($tasks)
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $task = Task::withTrashed()->find($id);
        $deleteType = null;

        if(!$task->trashed()){
            $task->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $task->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'Task has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $task = Task::withTrashed()->find($id);
        $task->restore();

        return response()->json([
            'status'   => true,
            'message'  => 'Task has been restored successfully!'
        ], 200);
    }
}
