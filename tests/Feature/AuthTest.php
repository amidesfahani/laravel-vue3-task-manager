<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('iran1404'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'iran1404',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token']);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('iran1404'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)->assertJson(['message' => 'Unauthorized']);
    }

    public function test_register_user_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Amid Esfahani',
            'email' => 'amid@laravel.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ]);

        $response->assertStatus(201)->assertJsonStructure(['token']);

        $this->assertDatabaseHas('users', [
            'email' => 'amid@laravel.com',
        ]);
    }

    public function test_register_user_with_invalid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Amid Esfahani',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_user_cannot_register_with_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'duplicate@laravel.com',
        ]);

        $response = $this->postJson('/api/register', [
            'name' => 'Amid',
            'email' => 'duplicate@laravel.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    public function test_login_response_contains_expected_data(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('iran1404'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'iran1404',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['token']);

        $this->assertAuthenticated();
    }

    public function test_user_can_register_and_then_login(): void
    {
        $registerResponse = $this->postJson('/api/register', [
            'name' => 'Taylor',
            'email' => 'taylor@laravel.com',
            'password' => 'laravellaravel',
            'password_confirmation' => 'laravellaravel',
        ]);

        $registerResponse->assertStatus(201)->assertJsonStructure(['token']);

        $loginResponse = $this->postJson('/api/login', [
            'email' => 'taylor@laravel.com',
            'password' => 'laravellaravel',
        ]);

        $loginResponse->assertStatus(200)->assertJsonStructure(['token']);
    }
}
