<?php

namespace App\Http\Controllers;



use App\Http\Requests\RegisterRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register (RegisterRequest $request)
    {
        $this->userService->createUser($request);
    }
}
