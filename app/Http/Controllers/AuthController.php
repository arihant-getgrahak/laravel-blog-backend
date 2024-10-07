<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Http;
use Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $data = [
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password),
            "type" => $request->type,
        ];
        $user = User::create($data);

        if (!$user) {
            return response()->json([
                "error" => "Unable to create user",
                "status" => false
            ], 500);
        }

        $this->sendEmail($user);

        return response()->json([
            "status" => true,
            "message" => "User registered successfully"
        ]);
    }

    public function login(LoginRequest $request)
    {
        $request->validated();

        $token = JWTAuth::attempt([
            "email" => $request->email,
            "password" => $request->password
        ]);

        if (!empty($token)) {

            return response()->json([
                "status" => true,
                "message" => "User logged in succcessfully",
                "token" => $token
            ]);
        }

        return response()->json([
            "status" => false,
            "message" => "Invalid details"
        ]);
    }

    public function profile()
    {
        $userdata = auth()->user();

        return response()->json([
            "status" => true,
            "message" => "Profile data",
            "data" => $userdata,
        ]);
    }


    public function logout()
    {
        $token = JWTAuth::getToken();

        $invalidate = JWTAuth::invalidate($token);

        if ($invalidate) {
            return response()->json([
                'meta' => [
                    'code' => 200,
                    'status' => 'success',
                    'message' => 'Successfully logged out',
                ],
                'data' => [],
            ]);
        }
    }

    protected function sendEmail(object $user)
    {
        $app_url = env("APP_URL");
        $token = Str::random(20);
        $url = "$app_url/api/verify/email/$user->id/$token";
        $data = [
            "email" => $user->email,
            "body" => "<a>$url</a>"
        ];
        $user->update([
            'remember_token' => $token
        ]);
        Http::post("https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzMjA0M2M1MjY4NTUzZDUxMzQi_pc", $data);
    }

}
