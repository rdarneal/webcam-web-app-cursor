<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\UserApiKey;
use Tests\TestCase;

class UserApiKeyModelTest extends TestCase
{
    public function test_api_key_is_encrypted_when_stored(): void
    {
        $user = User::factory()->create();
        $plainKey = 'sk-test-openai-key-123';
        
        $userApiKey = new UserApiKey([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'is_active' => true,
        ]);
        
        $userApiKey->setApiKey($plainKey);
        $userApiKey->save();
        
        // The encrypted key should not equal the plain key
        $this->assertNotEquals($plainKey, $userApiKey->encrypted_api_key);
        $this->assertNotEmpty($userApiKey->encrypted_api_key);
    }

    public function test_api_key_can_be_decrypted(): void
    {
        $user = User::factory()->create();
        $plainKey = 'sk-test-openai-key-123';
        
        $userApiKey = new UserApiKey([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'is_active' => true,
        ]);
        
        $userApiKey->setApiKey($plainKey);
        $userApiKey->save();
        
        $decryptedKey = $userApiKey->getApiKey();
        
        $this->assertEquals($plainKey, $decryptedKey);
    }

    public function test_mark_as_used_updates_timestamp(): void
    {
        $user = User::factory()->create();
        $userApiKey = UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'encrypted_api_key' => encrypt('test-key'),
            'is_active' => true,
        ]);
        
        $this->assertNull($userApiKey->last_used_at);
        
        $userApiKey->markAsUsed();
        $userApiKey->refresh();
        
        $this->assertNotNull($userApiKey->last_used_at);
    }

    public function test_active_scope(): void
    {
        $user = User::factory()->create();
        
        $activeKey = UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'encrypted_api_key' => encrypt('active-key'),
            'is_active' => true,
        ]);
        
        $inactiveKey = UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'elevenlabs',
            'encrypted_api_key' => encrypt('inactive-key'),
            'is_active' => false,
        ]);
        
        $activeKeys = UserApiKey::active()->get();
        
        $this->assertCount(1, $activeKeys);
        $this->assertEquals($activeKey->id, $activeKeys->first()->id);
    }

    public function test_for_service_scope(): void
    {
        $user = User::factory()->create();
        
        UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'encrypted_api_key' => encrypt('openai-key'),
            'is_active' => true,
        ]);
        
        UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'elevenlabs',
            'encrypted_api_key' => encrypt('elevenlabs-key'),
            'is_active' => true,
        ]);
        
        $openaiKeys = UserApiKey::forService('openai')->get();
        $elevenLabsKeys = UserApiKey::forService('elevenlabs')->get();
        
        $this->assertCount(1, $openaiKeys);
        $this->assertCount(1, $elevenLabsKeys);
        $this->assertEquals('openai', $openaiKeys->first()->service_name);
        $this->assertEquals('elevenlabs', $elevenLabsKeys->first()->service_name);
    }

    public function test_set_user_api_key_creates_new_key(): void
    {
        $user = User::factory()->create();
        $apiKey = 'sk-test-new-key';
        
        $userApiKey = UserApiKey::setUserApiKey($user->id, 'openai', $apiKey);
        
        $this->assertInstanceOf(UserApiKey::class, $userApiKey);
        $this->assertEquals($user->id, $userApiKey->user_id);
        $this->assertEquals('openai', $userApiKey->service_name);
        $this->assertEquals($apiKey, $userApiKey->getApiKey());
        $this->assertTrue($userApiKey->is_active);
    }

    public function test_set_user_api_key_updates_existing_key(): void
    {
        $user = User::factory()->create();
        $oldKey = 'sk-test-old-key';
        $newKey = 'sk-test-new-key';
        
        // Create initial key
        $userApiKey = UserApiKey::setUserApiKey($user->id, 'openai', $oldKey);
        $originalId = $userApiKey->id;
        
        // Update the key
        $updatedApiKey = UserApiKey::setUserApiKey($user->id, 'openai', $newKey);
        
        $this->assertEquals($originalId, $updatedApiKey->id);
        $this->assertEquals($newKey, $updatedApiKey->getApiKey());
        $this->assertTrue($updatedApiKey->is_active);
        
        // Ensure only one record exists
        $this->assertCount(1, UserApiKey::where('user_id', $user->id)
            ->where('service_name', 'openai')->get());
    }

    public function test_get_user_api_key_returns_correct_key(): void
    {
        $user = User::factory()->create();
        $apiKey = 'sk-test-key-retrieve';
        
        UserApiKey::setUserApiKey($user->id, 'openai', $apiKey);
        $retrievedKey = UserApiKey::getUserApiKey($user->id, 'openai');
        
        $this->assertEquals($apiKey, $retrievedKey);
    }

    public function test_get_user_api_key_returns_null_for_nonexistent(): void
    {
        $user = User::factory()->create();
        
        $retrievedKey = UserApiKey::getUserApiKey($user->id, 'nonexistent');
        
        $this->assertNull($retrievedKey);
    }

    public function test_get_user_api_key_returns_null_for_inactive_key(): void
    {
        $user = User::factory()->create();
        
        UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'encrypted_api_key' => encrypt('inactive-key'),
            'is_active' => false,
        ]);
        
        $retrievedKey = UserApiKey::getUserApiKey($user->id, 'openai');
        
        $this->assertNull($retrievedKey);
    }

    public function test_user_relationship(): void
    {
        $user = User::factory()->create();
        $userApiKey = UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'encrypted_api_key' => encrypt('test-key'),
            'is_active' => true,
        ]);
        
        $this->assertEquals($user->id, $userApiKey->user->id);
        $this->assertEquals($user->email, $userApiKey->user->email);
    }

    public function test_encrypted_api_key_is_hidden(): void
    {
        $user = User::factory()->create();
        $userApiKey = UserApiKey::create([
            'user_id' => $user->id,
            'service_name' => 'openai',
            'encrypted_api_key' => encrypt('test-key'),
            'is_active' => true,
        ]);
        
        $array = $userApiKey->toArray();
        
        $this->assertArrayNotHasKey('encrypted_api_key', $array);
    }
} 