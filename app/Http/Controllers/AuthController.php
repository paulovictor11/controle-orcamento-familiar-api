<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $this->validate($request, [
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $user = User::where('email', $request->email)->first();

            if (is_null($user) || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Invalid Credentials'
                ], 401);
            }

            $token = JWT::encode(['user' => $user], env('JWT_SECRET'), 'HS256');

            return response()->json([
                'token' => $token,
                'user'  => $user,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function register(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required',
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $data = $request->all();
            $data['password'] = Hash::make($request->password);

            $user = User::create($data);
            $token = JWT::encode(['user' => $user], env('JWT_SECRET'), 'HS256');

            return response()->json([
                'message' => 'User registered sucessfully',
                'token'   => $token,
                'user'    => $user
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function me(Request $request)
    {
        try {
            $token = $request->bearerToken();
            $data = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            return response()->json($data->user, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
