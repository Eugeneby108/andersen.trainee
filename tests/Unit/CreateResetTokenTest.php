<?php

namespace Tests\Unit;

use App\Models\ResetPassword;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class CreateResetTokenTest extends TestCase
{
    use DatabaseMigrations;

    private $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make(UserService::class);
        Artisan::call('passport:install');
    }

    public function testCreateResetToken()
    {
        $data = [
        'email' => 'qwerty1234@gmail.com',
        'password' => 'qwerty1234',
    ];
        $this->userService->createUser($data);
        $data = 'qwerty1234@gmail.com';
        $token = $this->userService->resetPass($data);
        $this->assertDatabaseHas('reset_passwords', [
            'token' => $this->userService->token
        ]);
    }
}
