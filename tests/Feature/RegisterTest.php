<?php

namespace Tests\Feature;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\RegisterController;

class RegisterTest extends TestCase
{

    private $registerController;

    protected function setUp(): void
    {
        parent::setUp();
        $this->registerController = new RegisterController();
    }

    protected function tearDown(): void
    {
        $this->registerController = null;
        parent::tearDown();
    }


    public function testRegister()

    {

        $response = $this->json('POST', 'register')->assertStatus(201);
        $response->assertJsonStructure([
            'token',
            'email',
            'password'
        ]);
}

}


