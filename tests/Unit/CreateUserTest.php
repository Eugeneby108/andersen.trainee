<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Services\UserService;

class CreateUserTest extends TestCase
{
    use DatabaseMigrations;

    private $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = $this->app->make(UserService::class);
        Artisan::call('passport:install');
    }

    public function testCreateUser()
    {
        $data = [
            'email' => 'qwerty1234@gmail.com',
            'password' => 'qwerty1234',
        ];
        $user = $this->userService->createUser($data);
        $this->assertInstanceOf(User::class, $user);
        $this->assertDatabaseHas('users', [
            'email' => 'qwerty1234@gmail.com'
        ]);
    }
}
