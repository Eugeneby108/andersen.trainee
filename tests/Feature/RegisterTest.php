<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function testRegister()
    {
        $data = [
            'email' => 'qwerty1234@gmail.com',
            'password' => 'qwerty1234',
            'c_password' => 'qwerty1234'
        ];

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];

        $this->json('POST', 'api/users', $data, $headers)
            ->assertStatus(201)
            ->assertJsonStructure([
            'token'
        ]);

        $this->json('POST', 'api/login', $data, $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'token'
            ]);

         $data['c_password'] = '40';

         $this->json('POST', 'api/users', $data, $headers)
             ->assertStatus(422);
     }
}
