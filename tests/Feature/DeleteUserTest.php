<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
    }

    public function testShowId()
    {
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

        $this->json('DELETE', 'api/users/'."$user2->id", $headers)
            ->assertStatus(403);
        $this->json('DELETE', 'api/users/'."$user1->id", $headers)
            ->assertStatus(204);
        $this->assertDatabaseHas('users', [
                'status' => User::Inactive,
                'name' => 'ert'
            ]);
    }
}
