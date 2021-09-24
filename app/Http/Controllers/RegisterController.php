<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewPasswordRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetRequest;
use App\Http\Requests\UpdateRequest;
use App\Http\Resources\ShowResource;
use App\Models\User;
use App\Services\UserService;
use Composer\DependencyResolver\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

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
        $dataReset = $request->email;


        $this->userService->resetPass($dataReset);

        $mail = $dataReset;
        Mail::to($mail)->send(new \App\Mail\PasswordReset($this->userService->token));
        return response('Email is sending successfully');
    }

    public function newPassword(NewPasswordRequest $request)
    {
        $dataNewPass = [
            'token' => $request->token,
            'password' => $request->password,
            'c_password'
        ];

        $this->userService->newPass($dataNewPass);
        return response('New password has been saved');
    }

    public function update(UpdateRequest $request, User $id)
    {
        $dataUpdate = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Gate::allows('update-user', $id)) {
        $this->userService->updateUser($dataUpdate, $id);
        }else{
            return response('Access denied', 403);
        }
        return response('Data has been saved successfully');
    }

    public function show()
    {
        $users = User::all();
        return response()->json(['users' => $users->pluck('email')]);
    }

    public function showId(User $id)
    {
        if (Gate::allows('show-user', $id)){
            return new ShowResource($id);
        }
        return response('Access denied', 403);
    }
}
