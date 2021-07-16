<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Resources\LessonResource;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{

    /**
     * @return LessonResource
     */

    public function index(Request $request)
    {

        $lang = $request->language;
        $course_type = $request->course_type;
        if ($course_type === 'additional') {
            $lessons = Lesson::whereHas('course_module.course', function ($query) use ( $course_type ) {
                $query->where('course_type', $course_type);
            })->where('is_free', 1);
        } else {
            if ($lang === 'all') {
                $lessons = Lesson::whereHas('course_module.course', function ($query) use ($course_type ) {
                    $query->where('course_type', $course_type);
                })->where('is_free', 1);
            } else {
                $lessons = Lesson::whereHas('course_module.course', function ($query) use ($lang, $course_type ) {
                    $query->where('teaching_language_id', $lang)->where('course_type', $course_type);
                })->where('is_free', 1);
            }

        }

        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 12;
        $count = $lessons->count();
        if ($page) {
            $skip = $take * ($page - 1);
            $lessons = $lessons->take($take)->skip($skip);
        } else {
            $lessons = $lessons->take($take)->skip(0);
        }
        return response()->json([
            'data' =>  LessonResource::collection($lessons->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }


    /**
     * @param $id
     * @return LessonResource
     */
    public function getFreeLesson($id)
    {
        $lesson = Lesson::findOrFail($id);
        return new LessonResource($lesson);
    }

    /**
     * @param $id
     * @return LessonResource
     */
    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        return new LessonResource($lesson);
    }
}
