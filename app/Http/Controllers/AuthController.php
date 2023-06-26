<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function signUp(Request $request)
    {
        $fields = $request->validate([
            'username' => ['required', 'min:4', 'max:60', Rule::unique('users', 'username')],
            'password' => ['required', 'min:8', 'max:65536']
        ]);

        $user = User::create([
            'username' => $fields['username'],
            'password' => bcrypt($fields['password']),
            'registered_at' => date('y/m/d')
        ]);

        $token = $user->createToken("token")->plainTextToken;

        $response = [
            'status' => "success",
            'token' => $token
        ];

        return response()->json($response, 201);
    }

    public function signIn(Request $request)
    {
        $fields = $request->validate([
           'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $fields['username'])->first();

        if($user->is_banned) {
            return response()->json([
                'status' => 'blocked',
                'message' => 'User blocked'
            ], 403);
        }

        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response()->json([
               "status" => 'invalid',
               'message' => 'Wrong username or password'
            ], 401);
        }

        $token = $user->createToken("token")->plainTextToken;

        $response = [
            'status' => "success",
            'token' => $token
        ];

        return response()->json($response, 201);
    }

    public function signOut(Request $request) {
        auth()->user()->tokens()->delete();

        return response()->json(['status' => 'success'], 201);
    }
}
