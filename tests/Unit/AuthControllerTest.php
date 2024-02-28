<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testRegister()
    {
        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->json('POST', '/api/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                ],
                'token' => true,
            ]);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email'],
        ]);
    }

    public function testLogin()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => Hash::make('password123'),
        ]);

        $loginData = [
            'email' => $user->email,
            'password' => 'password123',
        ];

        $response = $this->json('POST', '/api/login', $loginData);

        $response->assertStatus(201)
            ->assertJson([
                'user' => [
                    'email' => $user->email,
                ],
                'token' => true,
            ]);
    }

    public function testLogout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'log out']);
    }
}
