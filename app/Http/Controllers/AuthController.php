<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Http;
use Illuminate\Support\Facades\Hash;
use Str;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
        ];
        $user = User::create($data);

        if (! $user) {
            return response()->json([
                'error' => 'Unable to create user',
                'status' => false,
            ], 500);
        }

        $this->sendEmail($user);

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
        ]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if ($user->email_verified_at == null) {
            return response()->json([
                'error' => 'Please verify your email first',
                'status' => false,
            ], 500);
        }
        $token = JWTAuth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ]);

        if (! empty($token)) {
            return response()->json([
                'status' => true,
                'message' => 'User logged in succcessfully',
                'token' => $token,
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Invalid details',
        ]);
    }

    public function profile()
    {
        $userdata = User::with('media')->find(auth()->user()->id);
        $userdata['profile_image'] = $userdata->getFirstMediaUrl('user_photo');
        $userdata->makeHidden('media');

        return response()->json([
            'status' => true,
            'message' => 'Profile data',
            'data' => $userdata,
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

    public function verifyEmail(string $userId, string $token)
    {
        $user = User::where('id', $userId)->first();
        if ($user->remember_token != $token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 500);
        }
        if ($user) {
            if ($user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already verified',
                ], 500);
            } else {
                $user->update([
                    'email_verified_at' => now(),
                    'remember_token' => null,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Email verified successfully',
                ], 200);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 500);
    }

    protected function sendEmail(object $user)
    {
        $app_url = env('APP_URL');
        $token = Str::random(20);
        $url = "$app_url/api/verify/email/$user->id/$token";
        $data = [
            'email' => $user->email,
            'body' => "<a>$url</a>",
        ];
        $user->update([
            'remember_token' => $token,
        ]);
        Http::post('https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzMjA0M2M1MjY4NTUzZDUxMzQi_pc', $data);
    }
}
