<?php

namespace App\Http\Controllers;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Exception\BadResponseException;

class PassportController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'status' => true,
            'user' => $user
        ], 200);
    }

    public function login(Request $request)
    {
        $client = new Client();

        try {
            $response = $client->post(config('services.passport.login_endpoint'), [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => config('services.passport.client_id'),
                    'client_secret' => config('services.passport.client_secret'),
                    'username' => $request->username,
                    'password' => $request->password
                ]
            ]);

            return $response->getBody();

        } catch (BadResponseException $exception) {
            if ($exception->getCode() == 400) {
                return response()->json(['Invalid Request', $exception->getCode()]);
            } elseif ($exception->getCode() == 401) {
                return response()->json(['Invalid Credential', $exception->getCode()]);
            }

            return response()->json(['Something Wrong with Server, Please Try Again Later'], $exception->getCode());
        }
    }

    public function user()
    {
        return response()->json([
            'status' => true,
            'user' => auth()->user()
        ]);
    }

    public function logout(Request $request)
    {
        auth()->user()->token()->each(function($token, $key){
            $token->delete();
        });

        return response()->json(['Logged Out Successfully'], 200);
    }
}
