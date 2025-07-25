<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserApiKey;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    public function test_user_can_set_api_key(): void
    {
        $user = User::factory()->create();
        $apiKey = 'sk-test-openai-key-123';
        
        $userApiKey = $user->setApiKey('openai', $apiKey);
        
        $this->assertInstanceOf(UserApiKey::class, $userApiKey);
        $this->assertEquals('openai', $userApiKey->service_name);
        $this->assertEquals($user->id, $userApiKey->user_id);
        $this->assertTrue($userApiKey->is_active);
    }

    public function test_user_can_get_api_key(): void
    {
        $user = User::factory()->create();
        $apiKey = 'sk-test-openai-key-123';
        
        $user->setApiKey('openai', $apiKey);
        $retrievedKey = $user->getApiKey('openai');
        
        $this->assertEquals($apiKey, $retrievedKey);
    }

    public function test_user_can_check_if_has_api_key(): void
    {
        $user = User::factory()->create();
        
        $this->assertFalse($user->hasApiKey('openai'));
        
        $user->setApiKey('openai', 'sk-test-key');
        
        $this->assertTrue($user->hasApiKey('openai'));
        $this->assertFalse($user->hasApiKey('elevenlabs'));
    }

    public function test_user_can_get_all_api_keys(): void
    {
        $user = User::factory()->create();
        $openaiKey = 'sk-test-openai-key';
        $elevenLabsKey = 'el-test-elevenlabs-key';
        
        $user->setApiKey('openai', $openaiKey);
        $user->setApiKey('elevenlabs', $elevenLabsKey);
        
        $keys = $user->getApiKeys();
        
        $this->assertEquals($openaiKey, $keys['openai']);
        $this->assertEquals($elevenLabsKey, $keys['elevenlabs']);
    }

    public function test_user_returns_null_for_nonexistent_api_key(): void
    {
        $user = User::factory()->create();
        
        $this->assertNull($user->getApiKey('nonexistent'));
        $this->assertFalse($user->hasApiKey('nonexistent'));
    }

    public function test_user_api_key_relationship(): void
    {
        $user = User::factory()->create();
        $user->setApiKey('openai', 'test-key');
        
        $this->assertCount(1, $user->apiKeys);
        $this->assertEquals('openai', $user->apiKeys->first()->service_name);
    }

    public function test_user_active_api_keys_relationship(): void
    {
        $user = User::factory()->create();
        $user->setApiKey('openai', 'test-key');
        
        // Create an inactive key
        $inactiveKey = UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'inactive_service',
            'encrypted_api_key' => encrypt('inactive-key'),
            'is_active' => false,
        ]);
        
        $activeKeys = $user->activeApiKeys;
        
        $this->assertCount(1, $activeKeys);
        $this->assertEquals('openai', $activeKeys->first()->service_name);
    }
} 