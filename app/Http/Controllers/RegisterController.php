<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;



use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
            'c_password' => 'required|same:password'
        ]);

        $user = User::create([
            'email' => $request->email,
            'password' => $request->password
        ]);

        $token = $user->createToken('LaravelAuthApp')->accessToken;

        return response()->json(['token' => $token], 201);
    }
}
