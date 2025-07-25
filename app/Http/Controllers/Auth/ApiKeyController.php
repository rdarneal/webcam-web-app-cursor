<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UserApiKey;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiKeyController extends Controller
{
    /**
     * Store or update user API keys.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'openai_key' => 'nullable|string',
            'elevenlabs_key' => 'nullable|string',
        ]);

        $user = Auth::user();
        $stored = [];

        try {
            // Store OpenAI key if provided
            if ($request->filled('openai_key')) {
                $user->setApiKey('openai', $request->openai_key);
                $stored[] = 'OpenAI';
            }

            // Store ElevenLabs key if provided
            if ($request->filled('elevenlabs_key')) {
                $user->setApiKey('elevenlabs', $request->elevenlabs_key);
                $stored[] = 'ElevenLabs';
            }

            if (empty($stored)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No API keys provided'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'API keys stored successfully: ' . implode(', ', $stored),
                'stored_services' => $stored
            ]);

        } catch (\Exception $e) {
            Log::error('API key storage failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to store API keys',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update a single OpenAI API key.
     */
    public function storeOpenAI(Request $request): JsonResponse
    {
        $request->validate([
            'openai_key' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            $user->setApiKey('openai', $request->openai_key);

            return response()->json([
                'success' => true,
                'message' => 'OpenAI API key stored successfully',
                'service' => 'openai'
            ]);

        } catch (\Exception $e) {
            Log::error('OpenAI API key storage failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to store OpenAI API key',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store or update a single ElevenLabs API key.
     */
    public function storeElevenLabs(Request $request): JsonResponse
    {
        $request->validate([
            'elevenlabs_key' => 'required|string',
        ]);

        $user = Auth::user();

        try {
            $user->setApiKey('elevenlabs', $request->elevenlabs_key);

            return response()->json([
                'success' => true,
                'message' => 'ElevenLabs API key stored successfully',
                'service' => 'elevenlabs'
            ]);

        } catch (\Exception $e) {
            Log::error('ElevenLabs API key storage failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to store ElevenLabs API key',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test a single OpenAI API key.
     */
    public function testOpenAI(Request $request): JsonResponse
    {
        $user = Auth::user();

        try {
            if (!$user->hasApiKey('openai')) {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI API key not configured'
                ], 400);
            }

            $openaiKey = $user->getApiKey('openai');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiKey,
            ])->timeout(10)->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'OpenAI API key is valid',
                    'service' => 'openai'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI API key is invalid or expired',
                    'service' => 'openai'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'OpenAI API key validation failed: ' . $e->getMessage(),
                'service' => 'openai'
            ], 400);
        }
    }

    /**
     * Test a single ElevenLabs API key.
     */
    public function testElevenLabs(Request $request): JsonResponse
    {
        $user = Auth::user();

        try {
            if (!$user->hasApiKey('elevenlabs')) {
                return response()->json([
                    'success' => false,
                    'message' => 'ElevenLabs API key not configured'
                ], 400);
            }

            $elevenLabsKey = $user->getApiKey('elevenlabs');
            $response = Http::withHeaders([
                'xi-api-key' => $elevenLabsKey
            ])->timeout(10)->get('https://api.elevenlabs.io/v1/voices');

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'ElevenLabs API key is valid',
                    'service' => 'elevenlabs'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'ElevenLabs API key is invalid or expired',
                    'service' => 'elevenlabs'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'ElevenLabs API key validation failed: ' . $e->getMessage(),
                'service' => 'elevenlabs'
            ], 400);
        }
    }

    /**
     * Get user's API key status (without revealing actual keys).
     */
    public function status(): JsonResponse
    {
        $user = Auth::user();
        
        return response()->json([
            'success' => true,
            'api_keys' => [
                'openai' => [
                    'configured' => $user->hasApiKey('openai'),
                    'last_used' => $user->apiKeys()->forService('openai')->first()?->last_used_at?->toISOString(),
                ],
                'elevenlabs' => [
                    'configured' => $user->hasApiKey('elevenlabs'),
                    'last_used' => $user->apiKeys()->forService('elevenlabs')->first()?->last_used_at?->toISOString(),
                ],
            ]
        ]);
    }

    /**
     * Validate user's API keys.
     */
    public function validate(Request $request): JsonResponse
    {
        $user = Auth::user();
        $errors = [];
        $validServices = [];

        // Validate OpenAI key
        if ($user->hasApiKey('openai')) {
            try {
                $openaiKey = $user->getApiKey('openai');
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $openaiKey,
                ])->timeout(10)->get('https://api.openai.com/v1/models');

                if ($response->successful()) {
                    $validServices[] = 'OpenAI';
                } else {
                    $errors[] = 'OpenAI API key is invalid or expired';
                }
            } catch (\Exception $e) {
                $errors[] = 'OpenAI API key validation failed: ' . $e->getMessage();
            }
        } else {
            $errors[] = 'OpenAI API key not configured';
        }

        // Validate ElevenLabs key
        if ($user->hasApiKey('elevenlabs')) {
            try {
                $elevenLabsKey = $user->getApiKey('elevenlabs');
                $response = Http::withHeaders([
                    'xi-api-key' => $elevenLabsKey
                ])->timeout(10)->get('https://api.elevenlabs.io/v1/voices');

                if ($response->successful()) {
                    $validServices[] = 'ElevenLabs';
                } else {
                    $errors[] = 'ElevenLabs API key is invalid or expired';
                }
            } catch (\Exception $e) {
                $errors[] = 'ElevenLabs API key validation failed: ' . $e->getMessage();
            }
        } else {
            $errors[] = 'ElevenLabs API key not configured';
        }

        $isValid = empty($errors);

        return response()->json([
            'success' => $isValid,
            'message' => $isValid 
                ? 'All API keys are valid: ' . implode(', ', $validServices)
                : 'API key validation failed',
            'valid_services' => $validServices,
            'errors' => $errors
        ], $isValid ? 200 : 400);
    }

    /**
     * Delete user's API keys.
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'services' => 'required|array',
            'services.*' => 'in:openai,elevenlabs',
        ]);

        $user = Auth::user();
        $deleted = [];

        try {
            foreach ($request->services as $service) {
                $apiKey = $user->apiKeys()->forService($service)->first();
                if ($apiKey) {
                    $apiKey->delete();
                    $deleted[] = ucfirst($service);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'API keys deleted successfully: ' . implode(', ', $deleted),
                'deleted_services' => $deleted
            ]);

        } catch (\Exception $e) {
            Log::error('API key deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete API keys',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
