<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ShowAllTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function testShowAll(){

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        User::factory()->count(1)->create(
            [
                'name' => 'qwerty',
                'email' => 'ert@gmail.com'
            ]
        );

        $this->json('GET', 'api/users',$headers)
            ->assertJsonStructure([
                'email'
            ]);
    }
}
