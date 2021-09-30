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
use Barryvdh\DomPDF\Facade as PDF;

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

        return response()->json(['token' => $this->userService->token], 201);
    }

    public function login(RegisterRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];
        $status =User::where('email', $request->email)->value('status');
        if (auth()->attempt($credentials) and $status == 1) {
            $token = auth()->user()->createToken('LaravelAuthApp')->accessToken;
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['Error' => 'UnAuthorised'], 401);
        }
    }

    public function resetPassword(ResetRequest $request)
    {
        $dataReset = $request->email;

        $this->userService->resetPass($dataReset);

        $mail = $dataReset;
        Mail::to($mail)->send(new \App\Mail\PasswordReset($this->userService->token));
        return response()->json(['Success' => 'Email is sending successfully']);
    }

    public function newPassword(NewPasswordRequest $request)
    {
        $dataNewPass = [
            'token' => $request->token,
            'password' => $request->password,
            'c_password'
        ];

        $this->userService->newPass($dataNewPass);
        return response()->json(['Success' => 'New password has been saved']);
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
            return response()->json(['Error' => 'Access denied'], 403);
        }
        return response()->json(['Success' => 'Data has been saved successfully']);
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
        return response()->json(['Error' => 'Access denied'], 403);
    }

    public function delete(User $id)
    {
        if (!Gate::allows('delete-user', $id)){
            return response()->json(['Error' => 'Access denied'], 403);
        }
        $id->fill(['status' => $id::Inactive])->save();
        $pdf = PDF::loadView('pdf.invoice');
        Mail::send('pdf.invoice', [$id], function($message)use($id, $pdf) {
            $message->to($id['email'])
                ->attachData($pdf->output(), "invoice.pdf");
        });
        return response()->json(['Success' => 'Account got inactive and email is sending successfully'], 204);
    }
}
