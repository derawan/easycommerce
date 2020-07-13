<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\User;
use Illuminate\Support\Facades\Auth;

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
        $param["payload"]['password']= Hash::make($param["payload"]['password']);
        $param["payload"]['remember_token'] = Str::random(10);
        $user = User::create($param["payload"]);
        //$token = $user->createToken('OAuth Token')->accessToken;
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
        $user = User::where('email', $param["payload"]["email"])->first();
        if ($user) {
            if (Hash::check($param["payload"]["password"], $user->password)) {
                $token = $user->createToken('OAuth Token')->accessToken;
                $response = [
                    'success' => true,
                    'message' => __('Login Success'),
                    'payload' => $user->toArray(),
                    'token' => $token
                ];
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
        return response($response, 200);
    }
}
