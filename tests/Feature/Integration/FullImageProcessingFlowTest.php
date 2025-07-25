<?php

namespace Tests\Feature\Integration;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FullImageProcessingFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_complete_user_journey_from_registration_to_image_processing(): void
    {
        $this->mockExternalApis();

        // Step 1: User registers
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/register', [
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
            ]);

        $response->assertStatus(201);
        $userId = $response->json('user.id');

        // Step 2: Check authentication status
        $response = $this->get('/api/auth/check');
        $response->assertStatus(200)
            ->assertJson(['authenticated' => true]);

        // Step 3: Get user info (should show no API keys)
        $response = $this->withSession(['_token' => 'test-token'])
            ->get('/api/auth/user');
        
        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'has_openai_key' => false,
                    'has_elevenlabs_key' => false,
                ]
            ]);

        // Step 4: Try to process image without API keys (should fail)
        $image = UploadedFile::fake()->image('test.jpg');
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/process-image-proxy', ['image' => $image]);
        
        $response->assertStatus(400)
            ->assertJson(['error' => 'Missing API keys. Please configure your OpenAI and ElevenLabs API keys.']);

        // Step 5: Store API keys
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/api-keys', [
                'openai_key' => 'sk-test-openai-key',
                'elevenlabs_key' => 'el-test-elevenlabs-key',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Step 6: Check API key status
        $response = $this->withSession(['_token' => 'test-token'])
            ->get('/api/api-keys/status');
        
        $response->assertStatus(200)
            ->assertJson([
                'api_keys' => [
                    'openai' => ['configured' => true],
                    'elevenlabs' => ['configured' => true],
                ]
            ]);

        // Step 7: Test API keys
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/api-keys/validate');
        
        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Step 8: Process image successfully
        $image = UploadedFile::fake()->image('test.jpg');
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/process-image-proxy', [
                'image' => $image,
                'gpt_model' => 'gpt-4o-mini',
                'prompt_message' => 'Describe this image in detail',
                'voice_id' => 'pNInz6obpgDQGcFmaJgB',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'description',
                'audio_url',
                'source'
            ]);

        // Step 9: Verify API keys were marked as used
        $user = User::find($userId);
        $openaiKey = $user->apiKeys()->forService('openai')->first();
        $elevenLabsKey = $user->apiKeys()->forService('elevenlabs')->first();
        
        $this->assertNotNull($openaiKey->last_used_at);
        $this->assertNotNull($elevenLabsKey->last_used_at);

        // Step 10: Check updated API key status
        $response = $this->withSession(['_token' => 'test-token'])
            ->get('/api/api-keys/status');
        
        $response->assertStatus(200);
        $responseData = $response->json();
        $this->assertNotNull($responseData['api_keys']['openai']['last_used']);
        $this->assertNotNull($responseData['api_keys']['elevenlabs']['last_used']);

        // Step 11: Delete one API key
        $response = $this->withSession(['_token' => 'test-token'])
            ->delete('/api/api-keys', [
                'services' => ['openai']
            ]);

        $response->assertStatus(200);
        $this->assertFalse($user->fresh()->hasApiKey('openai'));
        $this->assertTrue($user->fresh()->hasApiKey('elevenlabs'));

        // Step 12: Logout
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/logout');

        $response->assertStatus(200);
        $this->assertGuest();
    }

    public function test_user_login_and_immediate_image_processing(): void
    {
        $this->mockExternalApis();

        // Create user with API keys
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user->setApiKey('openai', 'sk-test-key');
        $user->setApiKey('elevenlabs', 'el-test-key');

        // Login
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/auth/login', [
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'user' => [
                    'has_openai_key' => true,
                    'has_elevenlabs_key' => true,
                ]
            ]);

        // Immediately process image
        $image = UploadedFile::fake()->image('test.jpg');
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/process-image-proxy', ['image' => $image]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_api_key_management_flow(): void
    {
        $this->mockExternalApis();
        $user = $this->createAuthenticatedUser();

        // Store OpenAI key
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/api-keys/openai', [
                'openai_key' => 'sk-test-openai-key',
            ]);
        $response->assertStatus(200);

        // Test OpenAI key
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/api-keys/openai/test');
        $response->assertStatus(200);

        // Store ElevenLabs key
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/api-keys/elevenlabs', [
                'elevenlabs_key' => 'el-test-key',
            ]);
        $response->assertStatus(200);

        // Test ElevenLabs key
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/api-keys/elevenlabs/test');
        $response->assertStatus(200);

        // Validate all keys
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/api-keys/validate');
        $response->assertStatus(200);

        // Process image
        $image = UploadedFile::fake()->image('test.jpg');
        $response = $this->withSession(['_token' => 'test-token'])
            ->post('/api/process-image-proxy', ['image' => $image]);
        $response->assertStatus(200);

        // Delete all keys
        $response = $this->withSession(['_token' => 'test-token'])
            ->delete('/api/api-keys', [
                'services' => ['openai', 'elevenlabs']
            ]);
        $response->assertStatus(200);

        // Verify keys are gone
        $response = $this->withSession(['_token' => 'test-token'])
            ->get('/api/api-keys/status');
        $response->assertStatus(200)
            ->assertJson([
                'api_keys' => [
                    'openai' => ['configured' => false],
                    'elevenlabs' => ['configured' => false],
                ]
            ]);
    }
} 