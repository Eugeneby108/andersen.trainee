<?php

namespace Tests\Unit;

use App\Http\Requests\RegisterRequest;
use PHPUnit\Framework\TestCase;
use App\Services\UserService;


class CreateUserTest extends TestCase
{


    private $userService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new UserService();
        $this->userService->createUser();
    }

    protected function tearDown(): void
    {
        $this->userService = null;
        parent::tearDown();
    }



    public function testCreateUser()
    {


        $this->assertDatabaseHas('users', [
            'email' => 'qwerty@gmail.com',
            'password' => 'qwerty1234'
        ]);
    }
}





