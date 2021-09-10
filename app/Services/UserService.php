<?php

namespace App\Services;

use App\Http\Controllers\RegisterController;
use App\Models\User;

class UserService
{
        public function createUser (RegisterController $data)
        {
            $user = User::create($data);

            $user->createToken('LaravelAuthApp')->accessToken;
            return $user;


    }
}
