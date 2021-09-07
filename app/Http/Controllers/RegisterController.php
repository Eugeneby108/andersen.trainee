<?php

namespace App\Http\Controllers;



use App\Http\Requests\RegisterRequest;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {


        $user = User::create([
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['token' => $token], 201);
    }
}
