<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdminResource;
use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $admins = Admin::withTrashed()->orderBy('id', "desc");
        $page = $request->input('page') ? : 1;
        $take = $request->input('count') ? : 6;
        $count = $admins->count();

        if ($page) {
            $skip = $take * ($page - 1);
            $admins = $admins->take($take)->skip($skip);
        } else {
            $admins = $admins->take($take)->skip(0);
        }

        return response()->json([
            'data' =>  AdminResource::collection($admins->get()),
            'pagination'=>[
                'count_pages' => ceil($count / $take),
                'count' => $count
            ]
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRoles()
    {
        $roles = AdminRole::all();

        return response()->json([
            'data' =>  $roles,
        ], 200);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {;
        $validator = Validator::make($request->all(), [
            'name'    => 'required',
            'role_id'    => 'required',
            'email' => 'required|email|unique:admins,email,'. $id
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->only('name', 'phone', 'role_id', 'email');

        if ($request->has('avatar') && $request->avatar) {
            $file = $request->avatar;
            $avatarName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/admin/avatars';
            $file->storeAs($destinationPath, $avatarName);
            $data['avatar'] = $avatarName;
        }

        $admin = Admin::withTrashed()->where('id', $id)->update($data);

        if ($admin) {
            return response()->json([
                'data' => new AdminResource(Admin::find($id))
            ], 200);
        }
        return response()->json([
            'message' =>  'Server Error!'
        ], 402);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updatePassword(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'current_password'      => 'required|string|min:6',
            'new_password'              => 'required|string|min:6',
            'password_confirmation' => 'required_with:new_password|same:new_password|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
        $admin = Admin::find($id);

        if (!Hash::check($request->current_password, $admin->password)) {
            return response()->json([
                'current_password' => ['incorrect_current_password']
            ], 400);
        }

        $data = $validator->validated();

        $admin->password = Hash::make($data['new_password']);
        $admin->save();
        return response()->json([
            'data' => $admin
        ], 200);
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name'                  => 'required|string|between:2,100',
            'email'                 => 'required|string|email|max:100|unique:admins',
            'role_id'               => 'required|exists:admin_roles,id',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if ($request->has('avatar') && $data['avatar']) {
            $file = $request->avatar;
            $avatarName = time().'-'.uniqid().'.'.$file->extension();
            $destinationPath = 'public/admin/avatars';
            $file->storeAs($destinationPath, $avatarName);
            $data['avatar'] = $avatarName;
        }

        $admin = Admin::create($request->all());

        if ($admin) {
            return response()->json([
                'data' => new AdminResource($admin)
            ], 200);
        }

        return response()->json([
            'message' =>  'Server Error!'
        ], 402);
    }

    public function show($id)
    {
        $admin = Admin::withTrashed()->findOrFail($id);

        return response()->json([
            'data' => new AdminResource($admin)
        ], 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $status = Password::broker('admins')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::INVALID_USER) {
            return response()->json([
                'error'   => 'not_found',
                'message' => 'User not found!'
            ], 404);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'error'   => 'incorrect_credentials',
                'message' => 'Incorrect email or password!'
            ], 200);
        }
        return response()->json([
            'error'   => 'incorrect_credentials',
            'message' => 'Incorrect email or password!'
        ], 500);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (!$token = auth('admin')->attempt($validator->validated())) {
            return response()->json([
                'error'   => 'incorrect_credentials',
                'message' => 'Incorrect email or password!'
            ], 500);
        }

        return response()->json($this->createNewToken($token), 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    protected function sendResetResponse(Request $request)
    {
        $input = $request->only('email', 'token', 'password', 'password_confirmation');
        $validator = Validator::make($input, [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $response = Password::reset($input, function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();

        });
        if ($response == Password::PASSWORD_RESET) {
            $message = "Password reset successfully";
        } else {
            $message = "Email could not be sent to this email address";
        }
        $response = ['data' => '', 'message' => $message];
        return response()->json($response);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('admin')->logout();

        return response()->json(['message' => 'User successfully signed out'],
            200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth('admin')->user());
    }

    /**
     * @param $token
     *
     * @return array
     */
    protected function createNewToken($token)
    {
        return [
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => auth('admin')->factory()->getTTL() * 60,
            'user'         => auth('admin')->user()
        ];
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        $user = Admin::withTrashed()->find($id);
        $user->restore();

        return response()->json([
            'status'   => true,
            'data' => $user,
            'message'  => 'User has been restored successfully!'
        ], 200);
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {

        $user = Admin::withTrashed()->find($id);
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
}
