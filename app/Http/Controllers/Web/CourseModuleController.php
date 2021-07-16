<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseModuleResource;
use App\Models\CourseModule;
use Illuminate\Http\Request;

class CourseModuleController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function courseModule($id)
    {
        $course_modules = CourseModule::where('course_id', $id)->with('lessons.tasks');

        return response()->json([
            'data' => CourseModuleResource::collection($course_modules->get()),
        ], 200);
    }
}
