<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required_without:username|string',
            'username' => 'required_without:email|string',
            'password' => 'required|string',
        ]);

        $user = null;
        if ($request->email) {
            $user = User::where('email', $request->email)->first();
        } elseif ($request->username) {
            $user = User::where('username', $request->username)->first();
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email/Username atau password salah.'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user->load('roles')
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.'
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load(['roles', 'sekolah']));
    }
}
