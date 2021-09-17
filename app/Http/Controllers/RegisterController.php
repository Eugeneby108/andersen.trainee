<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\ResetPassword;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Password as RulesPassword;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\CanResetPassword;
use Laravel\Passport\Passport;
use Illuminate\Auth\Passwords\PasswordBroker;

class RegisterController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterRequest $request)
    {
        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        $user = $this->userService->createUser($data);

        return response(['token' => $this->userService->token], 201);
    }

    public function login(RegisterRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($credentials)) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'UnAuthorised'], 401);
        }
    }

    public function resetPassword(RegisterRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $token = str::random(60);
        ResetPassword::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token
        ]);
        $mail = $request->email;

        Mail::to($mail)->send(new \App\Mail\PasswordReset($token));
    }

    public function newPassword(RegisterRequest $request)
    {
        $newToken = ResetPassword::where('email', $request->email)->value('token','created_at');
        $newPassword = $request->password;
        $user = new User([
            'email' => $request->email,
            'password' => Hash::make($newPassword),
        ]);

        ResetPassword::where('email', $request->email)->delete();

        if(!$newToken){
            $this->resetPassword($request);
        }

    }
}
