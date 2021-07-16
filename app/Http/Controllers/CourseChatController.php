<?php

namespace App\Http\Controllers;

use App\Http\Resources\CourseChatResource;
use App\Models\CourseChat;
use Illuminate\Http\Request;

class CourseChatController extends Controller
{
    /**
     * Display a listing of the resource.
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $course_chats = CourseChat::orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $course_chats->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $course_chats = $course_chats->take($take)->skip($skip);
        } else {
            $course_chats = $course_chats->take($take)->skip(0);
        }

        return response()->json([
            'data' =>  CourseChatResource::collection($course_chats->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
