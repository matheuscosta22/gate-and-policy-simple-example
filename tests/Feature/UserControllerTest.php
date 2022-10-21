<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user_method()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($user)
            ->post('/api/users', [
                'name' => 'testing',
                'email' => 'test@test.com',
                'password' => 'test',
                'is_admin' => false
            ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_creation_user_method_when_authenticated_user_is_not_admin()
    {
        /** @var User $user */
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)
            ->post('/api/users', [
                'name' => 'testing',
                'email' => 'test@test.com',
                'password' => 'test',
                'is_admin' => false
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_read_user_method()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post('/api/users', [
                'name' => 'testing',
                'email' => 'test@test.com',
                'password' => 'test',
                'is_admin' => false
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $response2 = $this->actingAs($admin)
            ->get("/api/users/{$response->json()['id']}");

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->json()['id'], $response2->json()['id']);
    }

    public function test_read_user_method_when_authenticated_user_is_not_admin()
    {
        /** @var User $admin */
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)
            ->post('/api/users', [
                'name' => 'testing',
                'email' => 'test@test.com',
                'password' => 'test',
                'is_admin' => false
            ]);

        $response->assertStatus(Response::HTTP_OK);

        $user = User::find($response->json()['id']);

        $response2 = $this->actingAs($user)
            ->get("/api/users/{$response->json()['id']}");

        $response2->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
