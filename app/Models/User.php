<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's API keys.
     */
    public function apiKeys(): HasMany
    {
        return $this->hasMany(UserApiKey::class);
    }

    /**
     * Get active API keys for the user.
     */
    public function activeApiKeys(): HasMany
    {
        return $this->apiKeys()->active();
    }

    /**
     * Set an API key for a specific service.
     */
    public function setApiKey(string $serviceName, string $apiKey): UserApiKey
    {
        return UserApiKey::setUserApiKey($this->id, $serviceName, $apiKey);
    }

    /**
     * Get an API key for a specific service.
     */
    public function getApiKey(string $serviceName): ?string
    {
        return UserApiKey::getUserApiKey($this->id, $serviceName);
    }

    /**
     * Check if user has a valid API key for a service.
     */
    public function hasApiKey(string $serviceName): bool
    {
        return $this->getApiKey($serviceName) !== null;
    }

    /**
     * Get both OpenAI and ElevenLabs API keys if available.
     */
    public function getApiKeys(): array
    {
        return [
            'openai' => $this->getApiKey('openai'),
            'elevenlabs' => $this->getApiKey('elevenlabs'),
        ];
    }
}
