<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    public function test_user_can_register(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/register', $userData);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'User registered successfully',
            ])
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email']
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Verify user is authenticated after registration
        $this->assertAuthenticated();
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ])
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $userData = [
            'name' => 'John Doe',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different_password',
        ];

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/register', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'has_openai_key', 'has_elevenlabs_key']
            ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_login_includes_api_key_status(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);
        
        $user->setApiKey('openai', 'sk-test-key');

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'has_openai_key' => true,
                    'has_elevenlabs_key' => false,
                ]
            ]);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'wrong_password',
            ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'The provided credentials do not match our records.',
            ]);

        $this->assertGuest();
    }

    public function test_login_validation_errors(): void
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Logout successful',
            ]);

        $this->assertGuest();
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/logout');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Authentication required. Please log in to access this resource.',
            ]);
    }

    public function test_authenticated_user_can_get_user_data(): void
    {
        $user = $this->createAuthenticatedUser();
        $user->setApiKey('openai', 'sk-test-key');

        $response = $this->withSession(['_token' => 'test-token'])
            ->get('/api/auth/user');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'has_openai_key' => true,
                    'has_elevenlabs_key' => false,
                ]
            ]);
    }

    public function test_unauthenticated_user_cannot_get_user_data(): void
    {
        $response = $this->withSession(['_token' => 'test-token'])
            ->get('/api/auth/user');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Authentication required. Please log in to access this resource.',
            ]);
    }

    public function test_check_authentication_status_for_authenticated_user(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->get('/api/auth/check');

        $response->assertStatus(200)
            ->assertJson([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
    }

    public function test_check_authentication_status_for_guest_user(): void
    {
        $response = $this->get('/api/auth/check');

        $response->assertStatus(200)
            ->assertJson([
                'authenticated' => false,
                'user' => null,
            ]);
    }

    public function test_remember_me_functionality(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
                'remember' => true,
            ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }
} 