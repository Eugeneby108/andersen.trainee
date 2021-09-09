<?php

namespace App\Services;

use App\Http\Requests\RegisterRequest;
use App\Models\User;



class UserService
{
        public function createUser (RegisterRequest $request)
        {
            $data = array(
                'email',
                'password'
            );

            $user = User::create([
                'email' => $request->email,
                'password' => $request->password
            ]);

            $user->createToken('LaravelAuthApp')->accessToken;

            return $user;


    }
}
