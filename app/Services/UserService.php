<?php

namespace App\Services;

use App\Models\User;
use App\Models\ResetPassword;
use Illuminate\Support\Str;

class UserService
{
        public $token;

        public function createUser(array $data)
        {
            $user = User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password'])
            ]);

            $this->token = $user->createToken('LaravelAuthApp')->accessToken;

            return $user;
    }

        public function resetPass(array $dataReset)
        {
            $user = User::where('email', $dataReset['email'])->first();
            $token = ResetPassword::where('token', $dataReset['token'])->first();
            if($token){
                if ($token->created_at->copy()->addHours(2)->isPast()){
                    $token->delete();
                    return response()->json(['token error' => 'Try to get it again'], 400);
                }
                return response()->json(['token error' => 'Token is already exist'], 400);
            }
            $token = str::random(60);
            $this->token = $token;
            ResetPassword::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => $token
            ]);
        }

    public function newPass(array $dataNewPass)
    {
            $token = ResetPassword::where('token', $dataNewPass['token'])->first();
            if($token->created_at->copy()->addHours(2)->isPast()){
                $token->delete();
                return response()->json(['token error' => 'Try to get it again'], 404);
            }

            $id = $token->user_id;
            $user = User::findOrFail($id);
            $newPassword = $dataNewPass['password'];
            $user->fill(['password' => bcrypt($newPassword)]);
            $user->save();
            $token->delete();
    }
}
