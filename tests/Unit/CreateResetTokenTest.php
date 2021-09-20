<?php

namespace Tests\Unit;

use App\Models\ResetPassword;
use App\Services\UserService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\TestCase;

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
        ];
        $token = $this->userService->resetPass($data);
        $this->assertInstanceOf(ResetPassword::class, $token);
        $this->assertDatabaseHas('reset_passwords', [
            'token'
        ]);
    }
}
