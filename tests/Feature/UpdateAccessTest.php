<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateAccessTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function testUpdateAccess()
    {
        $data = [
            'name' => 'qwerty'
        ];

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $user = User::factory()->count(2)->create(
            [
                'name' => 'ert',
            ]
        );
        $user1 = $user->first();
        $user2 = $user->last();
        Passport::actingAs($user1);

        $this->json('PUT', 'api/users/'."$user2->id", $data, $headers)
            ->assertStatus(403);
        $this->json('PUT', 'api/users/'."$user1->id", $data, $headers)
            ->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'name' => 'qwerty'
        ]);
    }
}
