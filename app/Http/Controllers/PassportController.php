<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PassportController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'passwword' => bcrypt($request->password)
        ]);

        $token = $user->createToken('anemadhon')->accessToken;

        return response()->json([
            'status' => true,
            'token' => $token
        ], 200);
    }

    public function login(Request $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $token = auth()->user()->createToken('anemadhon')->accessToken;

        return response()->json([
            'status' => true,
            'token' => $token
        ], 200);
    }

    public function user()
    {
        return response()->json([
            'status' => true,
            'user' => auth()->user()
        ]);
    }
}
