<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Facebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use PhpParser\Node\Expr\AssignOp\Mod;
use PhpParser\Node\Stmt\DeclareDeclare;

class UserController extends Controller
{


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function chatNotifications()
    {
        return response()->json([
            'data' => auth('api')->user()->notifications->where('type', 'App\Notifications\InviteChatUser')
        ], 200);
    }

    /**
    * @return \Illuminate\Http\JsonResponse
    */
    public function notifications()
    {
        return response()->json([
            'data' => auth('api')->user()->notifications->where('type', '!=', 'App\Notifications\InviteChatUser')
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadNotificationsCount()
    {
        return response()->json([
            'data' => auth('api')->user()->unreadNotifications()->where('type', '!=', 'App\Notifications\InviteChatUser')->count()
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsReadNotifications()
    {
        auth('api')->user()->unreadNotifications->where('type', '!=', 'App\Notifications\InviteChatUser')->markAsRead();

        return $this->notifications();
    }

    /**
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function markNotificationAsRead($id)
    {
        $notification = auth('api')->user()->notifications()->find($id);
        if($notification) {
            $notification->markAsRead();
        }

        return $this->notifications();
    }

    /**
     * find user name field for login
     *
     * @return string
     */
    public function findUsername()
    {
        $username = request()->input('username');

        $fieldType = filter_var($username, FILTER_VALIDATE_EMAIL) ? 'email'
            : 'login';
        request()->merge([$fieldType => $username]);

        return $fieldType;
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = [
            $this->findUsername() => $request->username,
            'password'            => $request->password
        ];
        if (!$token = auth('api')->attempt($data)) {
            return response()->json([
                'error'   => 'incorrect_credentials',
                'message' => 'Неправильный логин или пароль!'
            ], 500);
        }

        $auth = auth('api')->user();

        if ($auth->locked) {
            return response()->json([
                'error'   => 'user_locked',
                'message' => 'Ваш аккаунт заблокирован!'
            ], 500);
        }

        return response()->json($this->createNewToken($token), 200);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws Facebook\Exceptions\FacebookSDKException
     */
    public function loginFb(Request $request)
    {

        $fb = new Facebook\Facebook([
            'app_id' => $request->userId,
            'app_secret' => '3cfc251d295f0e88ae395cfddf0c4e94',
            'default_graph_version' => 'v2.10',
        ]);

        $fb->setDefaultAccessToken($request->token);
        try {
            $response = $fb->get('/me/?fields=id,name,email,first_name,last_name,gender');
            $userNode = $response->getGraphUser();
        } catch(Facebook\Exception\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exception\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $fbData = [
            'email' => $userNode->getEmail(),
            'password' => bcrypt($userNode->getEmail()),
            'first_name' => $userNode->getFirstName(),
            'login' => $userNode->getFirstName(),
            'last_name' => $userNode->getLastName(),
            'site_id' => $userNode->getId(),
            'gender' =>$userNode->getGender(),
        ];

        if (!$fbData['email']) {
            return response()->json([
                'error' => 'Your facebook account does not have access permissions to your email address'
            ], 403);
        }
        $user = User::where('site_id', $fbData['site_id'])->where('email', $fbData['email'])->first();

        if (!$user){
             User::updateOrCreate(
                ['site_id' => $fbData['site_id']],
                [
                    'email' => $fbData['email'],
                    'password' => $fbData['password'],
                    'first_name' => $fbData['first_name'],
                    'login' => $fbData['login'],
                    'last_name' => $fbData['last_name'],
                    'gender' => $fbData['gender'],
                ]
            );
        }

        $data = [
            'login'    => $fbData['login'],
            'password' => $userNode->getEmail(),
        ];


        if (!$token = auth('api')->attempt($data)) {
            return response()->json([
                'error'   => 'incorrect_credentials',
                'message' => 'Неправильный логин или пароль!'
            ], 500);
        }

        if ($user && $user->locked) {
            return response()->json([
                'error'   => 'user_locked',
                'message' => 'Ваш аккаунт заблокирован!'
            ], 500);
        }

        return response()->json($this->createNewToken($token), 200);

    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginVk(Request $request)
    {
        $vkData = [
            'password' => bcrypt($request->user['id']),
            'first_name' => $request->user['first_name'],
            'last_name' => $request->user['last_name'],
            'site_id' => $request->user['id'],
            'login' => $request->user['first_name']
        ];

        $user = User::where('site_id', $vkData['site_id'])->where('first_name', $vkData['first_name'])->first();


        if (!$user){
             User::updateOrCreate(
                ['site_id' => $vkData['site_id']],
                [
                    'password' => $vkData['password'],
                    'first_name' => $vkData['first_name'],
                    'login' => $vkData['login'],
                    'last_name' => $vkData['last_name'],
                ]
            );;
        }

        $data = [
            'login'    => $vkData['login'],
            'password' => $request->user['id'],
        ];

        if (!$token = auth('api')->attempt($data)) {
            return response()->json([
                'error'   => 'incorrect_credentials',
                'message' => 'Неправильный логин или пароль!'
            ], 401);
        }

        if ($user && $user->locked) {
            return response()->json([
                'error'   => 'user_locked',
                'message' => 'Ваш аккаунт заблокирован!'
            ], 500);
        }

        return response()->json($this->createNewToken($token), 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function loginGoogle(Request $request)
    {

        $googleData = [
            'password' => bcrypt($request->id),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'site_id' => $request->id,
            'email' => $request->email
        ];

        $user = User::where('site_id', $googleData['site_id'])->first();


        if (!$user){
             User::updateOrCreate(
                ['site_id' => $googleData['site_id']],
                [
                    'password' => $googleData['password'],
                    'first_name' => $googleData['first_name'],
                    'last_name' => $googleData['last_name'],
                    'login' => $googleData['email'],
                    'email' => $googleData['email'],
                ]
            );;
        }

        $data = [
            'login'    => $googleData['email'],
            'password' => $googleData['site_id'],
        ];

        if (!$token = auth('api')->attempt($data)) {
            return response()->json([
                'error'   => 'incorrect_credentials',
                'message' => 'Неправильный логин или пароль!'
            ], 401);
        }

        if ($user && $user->locked) {
            return response()->json([
                'error'   => 'user_locked',
                'message' => 'Ваш аккаунт заблокирован!'
            ], 500);
        }

        return response()->json($this->createNewToken($token), 200);

    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name'            => 'required|string|between:2,100',
            'last_name'             => 'required|string|between:2,100',
            'email'                 => 'required|string|email|max:100|unique:users',
            'login'                 => 'required|string|max:100|unique:users',
            'phone'                 => 'required|string|between:2,100',
            'birthday'              => 'required|date',
            'gender'                => 'required|string',
            'password'              => 'required|string|min:6',
            'password_confirmation' => 'required_with:password|same:password|min:6'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
            ]
        ));

        event(new Registered($user));

        $data = [
            'email'    => $user->email,
            'password' => $request->password,
        ];

        if (!$token = auth('api')->attempt($data)) {
            return response()->json([
                'error'   => 'incorrect_credentials',
                'message' => 'Неправильный логин или пароль!'
            ], 401);
        }

        return response()->json($this->createNewToken($token), 200);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateAvatar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,jpg,png|max:10000'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $file = $request->avatar;
        $data = $validator->validate();
        $avatarName = time().'-'.uniqid().'.'.$file->extension();
        $destinationPath = 'public/users/avatars';
        $file->storeAs($destinationPath, $avatarName);

        $data[''] = $avatarName;
        $id = auth('api')->id();

        $user = User::find($id);
        $user->avatar = $avatarName;
        $user->save();

        return response()->json([
            'data' => $user
        ], 200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAvatar()
    {
        $id = auth('api')->id();

        $user = User::find($id);
        $user->avatar = null;

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
    public function update(Request $request)
    {
        $id = auth('api')->id();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
            'last_name'  => 'required|string|between:2,100',
            'email'      => 'required|string|email|max:100|unique:users,email,'. $id,
            'login'      => 'required|string|max:100|unique:users,login,'. $id,
            'phone'      => 'required|string|between:2,100',
            'birthday'   => 'required|date',
            'gender'     => 'required|string'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $data = $validator->validated();

        $user = User::find($id);
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        if ($user->email !== $data['email']){
            $user['email_verified_at'] = null;
        }
        $user->email = $data['email'];
        $user->login = $data['login'];
        $user->phone = $data['phone'];
        $user->birthday = $data['birthday'];
        $user->gender = $data['gender'];
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
    public function updatePassword(Request $request)
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'current_password'      => 'required|string|min:6',
            'new_password'              => 'required|string|min:6',
            'new_password_confirmation' => 'required_with:new_password|same:new_password|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'current_password' => ['incorrect_current_password']
            ], 400);
        }

        $data = $validator->validated();

        $user = User::find($user->id);
        $user->password = Hash::make($data['new_password']);
        $user->save();
        return response()->json([
            'data' => $user
        ], 200);
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendVerifyEmail()
    {
        $user = auth('api')->user();
        $user->sendEmailVerificationNotification();

        return response()->json([
            'status' => true,
            'message' => 'Verification Email sent successfully!'
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

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::INVALID_USER) {
            return response()->json([
                'error'   => 'not_found',
                'message' => 'User not found!'
            ], 403);
        }

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'error'   => 'reset_link_sent',
                'message' => 'Reset Link Sent!'
            ], 200);
        }

        return response()->json([
            'error'   => 'incorrect_credentials',
            'message' => 'Неправильный логин или пароль!'
        ], 500);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    protected function passwordReset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'new_password' => 'required|min:8|confirmed',
            'new_password_confirmation' => 'required_with:new_password|same:new_password|min:6'
        ]);

        $status = Password::reset(
            $request->only('email', 'new_password', 'new_password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'new_password' => Hash::make($password)
                ]);

                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return response()->json([
                'message' => 'Password successfully reset!'
            ],200);
        } else if ($status == Password::INVALID_TOKEN) {
            return response()->json([
                'message' => 'Your sent token is invalid!',
                'error' => 'invalid_token'
            ],
                403);
        } else {
            return response()->json([
                'message' => 'Error!',
                'error' => 'error'
            ], 403);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'User successfully signed out'],
            200);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth('api')->user());
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
            'expires_in'   => auth('api')->factory()->getTTL() * 60,
            'user'         => auth('api')->user()
        ];
    }

}
