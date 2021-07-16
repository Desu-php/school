<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\UserCourseModuleTest;
use Illuminate\Http\Request;

class CourseTestController extends Controller
{
    public function getCourseModuleTest (Request $request)
    {
        $course_module_test = UserCourseModuleTest::where('course_id', $request->id);

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 10;
        $count = $course_module_test->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $course_module_test = $course_module_test->take($take)->skip($skip);
        } else {
            $course_module_test = $course_module_test->take($take)->skip(0);
        }

        return response()->json([
            'data' => $course_module_test->get(),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }
}
