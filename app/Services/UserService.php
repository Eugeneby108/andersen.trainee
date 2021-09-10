<?php

namespace App\Services;

use App\Http\Controllers\RegisterController;
use App\Models\User;

class UserService
{
        public $token;

        public function createUser (array $data)
        {
            $user = User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);

            $this->token = $user->createToken('LaravelAuthApp')->accessToken;

            return $user;
    }
}
