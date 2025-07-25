<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class UserApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_name',
        'encrypted_api_key',
        'is_active',
        'last_used_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    protected $hidden = [
        'encrypted_api_key',
    ];

    /**
     * Get the user that owns the API key.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Set the API key (encrypts it before storing).
     */
    public function setApiKey(string $apiKey): void
    {
        $this->encrypted_api_key = Crypt::encryptString($apiKey);
    }

    /**
     * Get the decrypted API key.
     */
    public function getApiKey(): string
    {
        return Crypt::decryptString($this->encrypted_api_key);
    }

    /**
     * Update the last used timestamp.
     */
    public function markAsUsed(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Scope to get active keys only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get keys by service.
     */
    public function scopeForService($query, string $serviceName)
    {
        return $query->where('service_name', $serviceName);
    }

    /**
     * Get or create an API key for a user and service.
     */
    public static function setUserApiKey(int $userId, string $serviceName, string $apiKey): self
    {
        // Find existing or create new instance
        $userApiKey = self::where('user_id', $userId)
            ->where('service_name', $serviceName)
            ->first();

        if ($userApiKey) {
            // Update existing key
            $userApiKey->setApiKey($apiKey);
            $userApiKey->is_active = true;
            $userApiKey->save();
        } else {
            // Create new instance
            $userApiKey = new self([
                'user_id' => $userId,
                'service_name' => $serviceName,
                'is_active' => true,
            ]);
            $userApiKey->setApiKey($apiKey);
            $userApiKey->save();
        }

        return $userApiKey;
    }

    /**
     * Get an API key for a user and service.
     */
    public static function getUserApiKey(int $userId, string $serviceName): ?string
    {
        $userApiKey = self::where('user_id', $userId)
            ->where('service_name', $serviceName)
            ->active()
            ->first();

        return $userApiKey ? $userApiKey->getApiKey() : null;
    }
}
