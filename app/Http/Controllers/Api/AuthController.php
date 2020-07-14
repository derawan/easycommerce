<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
//use App\User;
use Illuminate\Support\Facades\Auth;

// Event
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{

    protected function ValidationError($validator) {
        return response(
            [
                'success' => false,
                'message' => __('Validation Error'),
                'message_detail' => $validator->errors()->all(),
                'errors'  =>$validator->errors()
            ],
            422);
    }

    protected function ParseParam(Request $request) {
        $param = (array)json_decode($request->getContent(),true);
        $state = ($param)?true:false;
        return [
            "success" => $state,
            "payload" => $state? $param : response([
                'success' => false,
                'message' => __('Validation Error'),
                'message_detail' => [
                    __("JSON parsing error")
                ],
                'errors' => [
                    "JSON" => 'JSON Parsing error'
                ]
            ])
        ];
    }

    protected function create(Request $request, $param) {

        $param['password']= Hash::make($param['password']);
        $param['remember_token'] = Str::random(10);

        $userModel = config('auth.providers.users.model');
        $user = $userModel::create($param);
        return $user;
    }

    public function register (Request $request) {
        $param = $this->ParseParam($request);
        if (!$param["success"])
            return $param["payload"];
        $validator = Validator::make($param["payload"], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        if ($validator->fails())
        {
            return $this->ValidationError($validator);
        }

        $user = $this->create($request, $param["payload"]);
        // trigger Registered Event
        event(new Registered($user));
        $response = [
            'success' => true,
            'message' => __('User Registered'),
            'payload'  => $user,
            //'token' => $token
        ];
        return response($response, 200);
    }

    public function login (Request $request) {
        $param = $this->ParseParam($request);
        if (!$param["success"])
            return $param["payload"];

        $validator = Validator::make($param["payload"], [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails())
        {
            return $this->ValidationError($validator);
        }
        $userModel = config('auth.providers.users.model');
        $user = $userModel::where('email', $param["payload"]["email"])->first();
        if ($user) {
            if (Hash::check($param["payload"]["password"], $user->password)) {
                $token = $user->createToken('OAuth Token')->accessToken;
                $response = [
                    'success' => true,
                    'message' => __('Login Success'),
                    'payload' => $user->toArray(),
                    'token' => $token
                ];
                // trigger login event
                event(new Login("api",$user,true));
                return response($response, 200);
            } else {
                $validator->errors()->add('password', 'Password mismatch');
                return $this->ValidationError($validator);
            }
        } else {
            $validator->errors()->add('email', __('User does not exists'));
            return $this->ValidationError($validator);
        }
    }

    public function logout (Request $request) {
        $token = Auth::user()->token();
        $user = Auth::user();
        $token->revoke();
        $response = [
            'success' => true,
            'message' => __('You have been successfully logged out!'),
            'payload' => $user
        ];
        event(new Logout("api",$user));
        return response($response, 200);
    }

    public function updateProfile(Request $request) {
        $param = $this->ParseParam($request);
        if (!$param["success"])
            return $param["payload"];
        $user = Auth::user();
       // dd($param["payload"]["token"]);

        $validator = Validator::make($param["payload"], [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'. $user->id,
            'password' => 'required|string|min:6|confirmed',
            'token' => 'required'
        ]);
        if ($validator->fails())
        {
            return $this->ValidationError($validator);
        }
        $userModel = config('auth.providers.users.model');
        $param['payload']['password'] = Hash::make($param['payload']['password']);
        try {
            $decrypted = Crypt::decrypt($param['payload']['token']);
            $par = json_decode($decrypted);
            if (json_decode(json_encode($user->updated_at))!=$par->updated_at)
            {
                $validator->errors()->add('user', __('User data has already been changed before'));
                return $this->ValidationError($validator);
            }
        } catch (DecryptException $e) {
            $validator->errors()->add('token', __('Token is invalid'));
            return $this->ValidationError($validator);
        }
        $user->update($param['payload']);
        $response = [
            'success' => true,
            'message' => __('User Profile Updated'),
            'payload'  => $user,
            //'token' => $token
        ];
        return $response;
    }
}
