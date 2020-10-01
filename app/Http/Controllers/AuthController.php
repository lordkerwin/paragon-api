<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if ($user && $user->admin) {
            if (Hash::check($request->password, $user->password)) {
                $token = $user->createToken('Laravel Password Grant Client')->accessToken;
                $response = [
                    'token' => $token,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ]
                ];
                return $this->respondSuccess($response, 'Login Successful');
            }
            return $this->respondError(null, 'Username or Password incorrect.', 422);
        }
        return $this->respondError(null, 'Forbidden', 403);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        return $this->respondSuccess(null, 'You have been successfully logged out!');
    }
}
