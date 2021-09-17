<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use App\Models\ResetPassword;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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

    public function resetPassword(ResetRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        $token = ResetPassword::where('token', $request->token)->first();
        if($token){
            if ($token->created_at->copy()->addHours(2)->isPast()){
                $token->delete();
                return response()->json(['token error' => 'Try to get it again'], 400);
            }
            return response()->json(['token error' => 'Token is already exist'], 400);
        }
        $token = str::random(60);
        ResetPassword::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token
        ]);

        $mail = $request->email;
        Mail::to($mail)->send(new \App\Mail\PasswordReset($token));
        return response('Email is sending succesfully');
    }

    public function newPassword(NewPasswordRequest $request)
    {
        $token = ResetPassword::where('token', $request->token)->first();
        if($token->created_at->copy()->addHours(2)->isPast()){
            $token->delete();
            return response()->json(['token error' => 'Try to get it again'], 404);
        }

        $id = $token->user_id;
        $user = User::findOrFail($id);
        $newPassword = $request->password;
        $user->fill(['password' => bcrypt($newPassword)]);
        $user->save();
        return response('New password has been saved');
    }
}
