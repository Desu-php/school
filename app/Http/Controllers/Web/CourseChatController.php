<?php

namespace App\Http\Controllers\Web;

use App\Events\CourseChatMessageSent;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseChatMessageResource;
use App\Http\Resources\CourseChatResource;
use App\Models\CourseChat;
use App\Models\CourseChatMessage;
use App\Models\User;
use App\Notifications\InviteChatUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class CourseChatController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index (Request $request)
    {
        $chats = CourseChat::with('course', 'users')->whereHas('users', function($q) {
            $q->where('user_id', auth('api')->id());
        })->where('status', 'accepted')->where('name', 'like' , '%'.$request->name.'%');

        if (!$request->name) {
            $take = $request->input('count');
            if ($take) {
                $chats = $chats->take($take);
            }
        }

        return CourseChatResource::collection($chats->get());
    }

    /**
     * @param Request $request
     *
     * @return CourseChatResource|\Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'count_persons' => 'required',
            'gender' => 'required',
            'age' => 'required',
            'course_id' => 'sometimes|exists:courses,id',
            'chat_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'name',
            'count_persons',
            'age',
            'gender',
            'course_id',
            'chat_type'
        );

        $data['status'] = 'waiting';

        $course_chat = CourseChat::create($data);

        $auth_id = auth('api')->id();
        $course_chat->users()->sync($auth_id);
        $users = User::where('id', '!=', $auth_id)->where('locked', false);



        if ($data["age"] !== "not_required"){
            $age_range = explode('-', $data["age"]);
            $users->where('birthday', '<=', date('Y-m-d', strtotime('-' . $age_range[0] . ' years')));
            if(isset($age_range[1])){
                $users->where('birthday', '>=', date('Y-m-d', strtotime('-' . $age_range[1] . ' years')));
            }
        }

        if ($data["gender"] !== "not_required") {
            $users->where('gender', $data["gender"]);
        }

        if($data['count_persons']) {
            $users->inRandomOrder()->limit($data['count_persons']);
        }

        $users = $users->get();

        foreach($users as $user) {
            $user->course_chat()->create(
                ['user_id' => $user->id],
                ['status' => 'pending']
            );
        }

        Notification::send($users, new InviteChatUser(auth('api')->user(), $course_chat));

        return response()->json([
            'message' => 'Chat created successfully!'
        ], 200);
    }

    public function acceptInvitation($id)
    {
        dd($id);
    }

    public function declineInvitation($id)
    {
        dd($id);
    }

    /**
     * @param Request $request
     *
     * @return CourseChatResource|\Illuminate\Http\JsonResponse
     */
    public function chatLeave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $course_chat = CourseChat::find($request->id);

        $course_chat->users()->detach(auth('api')->id());

        $id = $request->id;
        $coursChatUser = CourseChat::with('users')->whereHas('users', function($q)  use ($id){
            $q->where('course_chat_id', $id);
        })->get();
       if (!count($coursChatUser)) {
           CourseChat::where('id',$id)->delete();
       }

        return new CourseChatResource($course_chat);
    }


    /**
     * @param $id
     *
     * @return CourseChatResource
     */
    public function show ($id)
    {
        $course_chat = CourseChat::findOrFail($id);

        return new CourseChatResource($course_chat->load('course_chat_messages.user', 'users'));
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return CourseChatMessageResource|\Illuminate\Http\JsonResponse
     */
    public function sendMessage (Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only(
            'message'
        );

        $message = CourseChatMessage::create([
            'message' => $data['message'],
            'user_id' => auth('api')->id(),
            'course_chat_id' => $id
        ]);

        $data = new CourseChatMessageResource($message->load('user'));

        broadcast(new CourseChatMessageSent($data, 'course-chat.' . $id))->toOthers();

        return $data;
    }
}
