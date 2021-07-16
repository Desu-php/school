<?php

namespace App\Http\Controllers;

use App\Events\ChatExpertMessageSent;
use App\Events\StartExpertVideoChat;
use App\Models\ChatExpert;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatExpertController extends Controller
{
    /**
     * @param $course_id
     * @param $user_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ($course_id, $user_id) {
        $user_course = UserCourse::where('course_id', $course_id)->where('user_id', $user_id)->first();

        $chats = ChatExpert::where('user_course_id', $user_course->id)->where('user_id', $user_id)->with('user', 'admin')->get();

        return response()->json([
            'data' => $chats
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'course_id' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user_course = UserCourse::where('course_id', $request->course_id)->where('user_id', $request->user_id)->first();
        $data = $request->all();
        $data['user_course_id'] = $user_course->id;
        $data['sender_is_user'] = false;
        $data['admin_id'] = auth('admin')->id();

        $chat_expert = ChatExpert::create($data);

        broadcast(new ChatExpertMessageSent($chat_expert->load('user', 'admin'), 'chat-expert.' . $user_course->id))->toOthers();

        return response()->json([
            'data' => $chat_expert
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function callUser (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'signal_data' => 'required',
            'user_to_call' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data['userToCall'] = $request->user_to_call;
        $data['signalData'] = $request->signal_data;
        $data['from'] = auth('api')->id();
        $data['type'] = 'incomingCall';

        broadcast(new StartExpertVideoChat($data))->toOthers();
    }

    /**
     * @param Request $request
     */
    public function acceptCall(Request $request)
    {
        $data['signal'] = $request->signal;
        $data['to'] = $request->to;
        $data['type'] = 'callAccepted';
        broadcast(new StartExpertVideoChat($data))->toOthers();
    }
}
