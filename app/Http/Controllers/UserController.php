<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\SupportTicket;
use App\Models\SupportTicketCategory;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $users = User::withTrashed()->orWhere('first_name', 'like' , '%'.$request->user_name.'%')->orWhere('last_name', 'like' , '%'.$request->user_name.'%');
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $users->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $users = $users->take($take)->skip($skip);
        } else {
            $users = $users->take($take)->skip(0);
        }



        return response()->json([
            'data' =>  UserResource::collection($users->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json([
            'data' =>  new UserResource($user)
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'status'   => true,
            'message'  => 'User has been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $deleteType = null;

        if(!$user->trashed()){
            $user->delete();
            $deleteType = 'delete';
        }
        else {
            $deleteType = 'forceDelete';
            $user->forceDelete();
        }

        return response()->json([
            'status' => true,
            'deleteType' => $deleteType,
            'message' => 'User has been deleted successfully!'
        ], 200);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function lockUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        if($user->locked){
            $data = [
                'locked' => false
            ];
        }
        else {
            $data = [
                'locked' => true
            ];
        }

        User::withTrashed()->where('id', $id)->update($data);

        $user = User::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new UserResource($user),
            'message' => 'User has been deleted successfully!'
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateUserPassword(Request $request)
    {
        $user = User::withTrashed()->findOrFail($request->user_id);

        $validator = Validator::make($request->all(), [
            'user_id'                   => 'required',
            'new_password'              => 'required|string|min:6',
            'new_password_confirmation' => 'required_with:new_password|same:new_password|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();

        $user->password = Hash::make($data['new_password']);
        $user->save();
        return response()->json([
            'data' => $user
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateCourseLevel(Request $request)
    {
        $user = User::withTrashed()->findOrFail($request->user_id);

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'courses' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        return response()->json([
            'data' => $user
        ], 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCourseTariff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'courses' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        foreach ($request->courses as $course) {
            $userCourse = UserCourse::where('user_id', $request->user_id)->where('course_id', $course['id']);
            $userCourse->update(['course_tariff_id' => $course['course_tariff_id']]);
        }

        return response()->json([
            'data' => $request->courses
        ], 200);
    }

    public function userBoughtCourses($id)
    {
        $boughtCourses = UserCourse::where('user_id', $id)->with('course', 'course.course_level', 'course_tariff', 'course.tariffs')->get();

        return response()->json([
            'data' => $boughtCourses,
        ], 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendUserMessage(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'user_id'    => 'required',
            'message'    => 'required',
            'support_ticket_category_id' => 'required|exists:support_ticket_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data['admin_id'] = auth('admin')->id();
        $data['sender_is_user'] = false;


        $support_ticket = SupportTicket::create($data);
        $support_ticket->support_ticket_messages()->create($data);
        $support_ticket->load('support_ticket_category', 'support_ticket_messages');

        return response()->json([
            'data' => $support_ticket
        ], 200);
    }
}
