<?php

namespace App\Http\Controllers\Web;

use App\Events\ChatExpertMessageSent;
use App\Http\Controllers\Controller;
use App\Models\ChatExpert;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Events\StartExpertVideoChat;

class ChatExpertController extends Controller
{
    /**
     * @param $course_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index ($course_id)
    {

        $user_course = UserCourse::where('course_id', $course_id)->where('user_id', auth('api')->id())->first();

        $chats = ChatExpert::where('user_course_id', $user_course->id)->where('user_id', auth('api')->id())->with('user', 'admin')->get();

        return response()->json([
            'data' => $chats
        ]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'course_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user_course = UserCourse::where('course_id', $request->course_id)->where('user_id', auth('api')->id())->first();

        $data = $request->only('message');
        $data['user_course_id'] = $user_course->id;
        $data['user_id'] = auth('api')->id();
        $data['sender_is_user'] = true;

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
    public function callExpert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'signal_data' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data['signalData'] = $request->signal_data;
        $data['from'] = auth('api')->user();
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
