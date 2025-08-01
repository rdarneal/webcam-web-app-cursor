<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ImageProcessingController extends Controller
{
    // Default configuration values
    const DEFAULT_GPT_MODEL = 'gpt-4o-mini';
    const DEFAULT_VOICE_ID = 'pNInz6obpgDQGcFmaJgB'; // Adam voice
    const DEFAULT_PROMPT = 'Please describe this image in detail. Focus on what you see, including objects, people, activities, colors, and the overall scene. Make it engaging and descriptive as if you\'re helping someone who cannot see the image.';

    /**
     * Process image using user's stored API keys (Backend Proxy approach).
     */
    public function processImageProxy(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $request->validate([
                'image' => 'required|image|max:10240', // 10MB max
                'gpt_model' => 'sometimes|string|max:100',
                'prompt_message' => 'sometimes|string|max:1000',
                'voice_id' => 'sometimes|string|max:100'
            ]);

            $user = Auth::user();
            
            // Check if user has both required API keys
            if (!$user->hasApiKey('openai') || !$user->hasApiKey('elevenlabs')) {
                return response()->json([
                    'error' => 'Missing API keys. Please configure your OpenAI and ElevenLabs API keys.',
                    'missing_keys' => [
                        'openai' => !$user->hasApiKey('openai'),
                        'elevenlabs' => !$user->hasApiKey('elevenlabs'),
                    ]
                ], 400);
            }

            $image = $request->file('image');
            
            // Get advanced configuration with defaults
            $gptModel = $request->get('gpt_model', self::DEFAULT_GPT_MODEL);
            $promptMessage = $request->get('prompt_message', self::DEFAULT_PROMPT);
            $voiceId = $request->get('voice_id', self::DEFAULT_VOICE_ID);
            
            // Store the image temporarily
            $imagePath = $image->store('temp', 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);

            // Step 1: Convert image to text using user's OpenAI API key
            try {
                $imageDescription = $this->convertImageToTextWithUserKey(
                    $fullImagePath, 
                    $user->getApiKey('openai'),
                    $gptModel, 
                    $promptMessage
                );
                
                if (!$imageDescription) {
                    return response()->json([
                        'error' => 'Failed to analyze image'
                    ], 500);
                }
            } catch (\Exception $e) {
                // Check if this is a structured API error
                $errorData = json_decode($e->getMessage(), true);
                if ($errorData && isset($errorData['service'])) {
                    return response()->json([
                        'error' => $errorData['display_message'],
                        'api_error' => $errorData
                    ], 500);
                }
                throw $e; // Re-throw if not a structured error
            }

            // Step 2: Convert text to speech using user's ElevenLabs API key
            try {
                $audioUrl = $this->convertTextToSpeechWithUserKey(
                    $imageDescription, 
                    $user->getApiKey('elevenlabs'),
                    $voiceId
                );
                
                if (!$audioUrl) {
                    return response()->json([
                        'error' => 'Failed to generate audio from text'
                    ], 500);
                }
            } catch (\Exception $e) {
                // Check if this is a structured API error
                $errorData = json_decode($e->getMessage(), true);
                if ($errorData && isset($errorData['service'])) {
                    return response()->json([
                        'error' => $errorData['display_message'],
                        'api_error' => $errorData
                    ], 500);
                }
                throw $e; // Re-throw if not a structured error
            }
            
            // Mark API keys as used
            $user->apiKeys()->forService('openai')->first()?->markAsUsed();
            $user->apiKeys()->forService('elevenlabs')->first()?->markAsUsed();
            
            // Clean up temporary image
            Storage::disk('public')->delete($imagePath);

            return response()->json([
                'success' => true,
                'description' => $imageDescription,
                'audio_url' => $audioUrl,
                'source' => 'user-keys-proxy'
            ]);

        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            
            // Check if this is a structured API error
            $errorData = json_decode($e->getMessage(), true);
            if ($errorData && isset($errorData['service'])) {
                return response()->json([
                    'error' => $errorData['display_message'],
                    'api_error' => $errorData
                ], 500);
            }
            
            return response()->json([
                'error' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function processImage(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $request->validate([
                'image' => 'required|image|max:10240', // 10MB max
                'gpt_model' => 'sometimes|string|max:100',
                'prompt_message' => 'sometimes|string|max:1000',
                'voice_id' => 'sometimes|string|max:100'
            ]);

            $image = $request->file('image');
            
            // Get advanced configuration with defaults
            $gptModel = $request->get('gpt_model', self::DEFAULT_GPT_MODEL);
            $promptMessage = $request->get('prompt_message', self::DEFAULT_PROMPT);
            $voiceId = $request->get('voice_id', self::DEFAULT_VOICE_ID);
            
            // Store the image temporarily
            $imagePath = $image->store('temp', 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);

            // Step 1: Convert image to text using OpenAI Vision API
            $imageDescription = $this->convertImageToText($fullImagePath, $gptModel, $promptMessage);
            
            if (!$imageDescription) {
                return response()->json([
                    'error' => 'Failed to analyze image'
                ], 500);
            }

            // Step 2: Convert text to speech using Eleven Labs API
            $audioUrl = $this->convertTextToSpeech($imageDescription, $voiceId);
            
            // Clean up temporary image
            Storage::disk('public')->delete($imagePath);

            return response()->json([
                'success' => true,
                'description' => $imageDescription,
                'audio_url' => $audioUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fallback processing method using server-side API keys
     * This is used when users don't want to provide their own keys
     */
    public function processImageWithServerKeys(Request $request): JsonResponse
    {
        try {
            // Validate the request
            $request->validate([
                'image' => 'required|image|max:10240', // 10MB max
                'gpt_model' => 'sometimes|string|max:100',
                'prompt_message' => 'sometimes|string|max:1000',
                'voice_id' => 'sometimes|string|max:100'
            ]);

            $image = $request->file('image');
            
            // Get advanced configuration with defaults
            $gptModel = $request->get('gpt_model', self::DEFAULT_GPT_MODEL);
            $promptMessage = $request->get('prompt_message', self::DEFAULT_PROMPT);
            $voiceId = $request->get('voice_id', self::DEFAULT_VOICE_ID);
            
            // Store the image temporarily
            $imagePath = $image->store('temp', 'public');
            $fullImagePath = storage_path('app/public/' . $imagePath);

            // Step 1: Convert image to text using OpenAI Vision API
            $imageDescription = $this->convertImageToText($fullImagePath, $gptModel, $promptMessage);
            
            if (!$imageDescription) {
                return response()->json([
                    'error' => 'Failed to analyze image'
                ], 500);
            }

            // Step 2: Convert text to speech using Eleven Labs API
            $audioUrl = $this->convertTextToSpeech($imageDescription, $voiceId);
            
            // Clean up temporary image
            Storage::disk('public')->delete($imagePath);

            return response()->json([
                'success' => true,
                'description' => $imageDescription,
                'audio_url' => $audioUrl,
                'source' => 'server-keys'
            ]);

        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            
            return response()->json([
                'error' => 'Failed to process image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate API keys endpoint
     * Allows users to test their API keys without processing images
     */
    public function validateApiKeys(Request $request): JsonResponse
    {
        $request->validate([
            'openai_key' => 'required|string',
            'elevenlabs_key' => 'required|string'
        ]);

        $errors = [];
        
        try {
            // Test OpenAI key
            $openaiResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->openai_key,
            ])->timeout(10)->get('https://api.openai.com/v1/models');

            if (!$openaiResponse->successful()) {
                $errors[] = 'OpenAI API key is invalid';
            }
        } catch (\Exception $e) {
            $errors[] = 'OpenAI API key validation failed: ' . $e->getMessage();
        }

        try {
            // Test ElevenLabs key
            $elevenResponse = Http::withHeaders([
                'xi-api-key' => $request->elevenlabs_key
            ])->timeout(10)->get('https://api.elevenlabs.io/v1/voices');

            if (!$elevenResponse->successful()) {
                $errors[] = 'ElevenLabs API key is invalid';
            }
        } catch (\Exception $e) {
            $errors[] = 'ElevenLabs API key validation failed: ' . $e->getMessage();
        }

        if (empty($errors)) {
            return response()->json([
                'success' => true,
                'message' => 'Both API keys are valid'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'errors' => $errors
            ], 400);
        }
    }

    /**
     * Convert image to text using user's API key.
     */
    private function convertImageToTextWithUserKey(string $imagePath, string $openaiApiKey, string $gptModel = null, string $promptMessage = null): ?string
    {
        try {
            // Use provided values or defaults
            $model = $gptModel ?? self::DEFAULT_GPT_MODEL;
            $prompt = $promptMessage ?? self::DEFAULT_PROMPT;

            // Convert image to base64
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiApiKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:' . $mimeType . ';base64,' . $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 300
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            } else {
                $errorBody = $response->body();
                Log::error('OpenAI API error (user key): ' . $errorBody);
                
                // Parse OpenAI error for frontend display
                $errorData = json_decode($errorBody, true);
                $errorMessage = $errorData['error']['message'] ?? 'Unknown OpenAI error';
                $errorType = $errorData['error']['type'] ?? 'unknown';
                
                // Store error details for frontend
                throw new \Exception(json_encode([
                    'service' => 'openai',
                    'type' => $errorType,
                    'message' => $errorMessage,
                    'display_message' => 'OpenAI Error: ' . $errorMessage
                ]));
            }

        } catch (\Exception $e) {
            // Check if this is a structured API error that should bubble up
            $errorData = json_decode($e->getMessage(), true);
            if ($errorData && isset($errorData['service'])) {
                // Re-throw structured errors so they can be handled by the main controller
                throw $e;
            }
            
            Log::error('Image to text conversion failed (user key): ' . $e->getMessage());
            return 'I can see an image, but image analysis encountered an error.';
        }
    }

    /**
     * Convert text to speech using user's API key.
     */
    private function convertTextToSpeechWithUserKey(string $text, string $elevenLabsApiKey, string $voiceId = null): ?string
    {
        try {
            $voice = $voiceId ?? self::DEFAULT_VOICE_ID;

            $response = Http::withHeaders([
                'Accept' => 'audio/mpeg',
                'Content-Type' => 'application/json',
                'xi-api-key' => $elevenLabsApiKey
            ])->timeout(30)->post("https://api.elevenlabs.io/v1/text-to-speech/{$voice}", [
                'text' => $text,
                'model_id' => 'eleven_monolingual_v1',
                'voice_settings' => [
                    'stability' => 0.5,
                    'similarity_boost' => 0.5
                ]
            ]);

            if ($response->successful()) {
                // Store the audio file
                $audioFileName = 'audio_' . time() . '.mp3';
                $audioPath = 'audio/' . $audioFileName;
                
                Storage::disk('public')->put($audioPath, $response->body());
                
                return Storage::disk('public')->url($audioPath);
            } else {
                $errorBody = $response->body();
                Log::error('Eleven Labs API error (user key): ' . $errorBody);
                
                // Parse ElevenLabs error for frontend display
                $errorData = json_decode($errorBody, true);
                $errorStatus = $errorData['detail']['status'] ?? 'unknown';
                $errorMessage = $errorData['detail']['message'] ?? 'Unknown ElevenLabs error';
                
                // Store error details for frontend
                throw new \Exception(json_encode([
                    'service' => 'elevenlabs',
                    'type' => $errorStatus,
                    'message' => $errorMessage,
                    'display_message' => 'ElevenLabs Error: ' . $errorMessage
                ]));
            }

        } catch (\Exception $e) {
            // Check if this is a structured API error that should bubble up
            $errorData = json_decode($e->getMessage(), true);
            if ($errorData && isset($errorData['service'])) {
                // Re-throw structured errors so they can be handled by the main controller
                throw $e;
            }
            
            Log::error('Text to speech conversion failed (user key): ' . $e->getMessage());
            return null;
        }
    }

    private function convertImageToText(string $imagePath, string $gptModel = null, string $promptMessage = null): ?string
    {
        try {
            $openaiApiKey = env('OPENAI_API_KEY');
            
            if (!$openaiApiKey) {
                Log::warning('OpenAI API key not configured');
                return 'I can see an image, but image analysis is not currently configured. Please set your OpenAI API key in the .env file.';
            }

            // Use provided values or defaults
            $model = $gptModel ?? self::DEFAULT_GPT_MODEL;
            $prompt = $promptMessage ?? self::DEFAULT_PROMPT;

            // Convert image to base64
            $imageData = base64_encode(file_get_contents($imagePath));
            $mimeType = mime_content_type($imagePath);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $openaiApiKey,
                'Content-Type' => 'application/json'
            ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => $prompt
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => 'data:' . $mimeType . ';base64,' . $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'max_tokens' => 300
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? null;
            } else {
                Log::error('OpenAI API error: ' . $response->body());
                return 'I can see an image, but there was an error analyzing it with the AI service.';
            }

        } catch (\Exception $e) {
            Log::error('Image to text conversion failed: ' . $e->getMessage());
            return 'I can see an image, but image analysis encountered an error.';
        }
    }

    private function convertTextToSpeech(string $text, string $voiceId = null): ?string
    {
        try {
            $elevenLabsApiKey = env('ELEVEN_LABS_API_KEY');
            $voice = $voiceId ?? self::DEFAULT_VOICE_ID;
            
            if (!$elevenLabsApiKey) {
                Log::warning('Eleven Labs API key not configured');
                return null;
            }

            $response = Http::withHeaders([
                'Accept' => 'audio/mpeg',
                'Content-Type' => 'application/json',
                'xi-api-key' => $elevenLabsApiKey
            ])->timeout(30)->post("https://api.elevenlabs.io/v1/text-to-speech/{$voice}", [
                'text' => $text,
                'model_id' => 'eleven_monolingual_v1',
                'voice_settings' => [
                    'stability' => 0.5,
                    'similarity_boost' => 0.5
                ]
            ]);

            if ($response->successful()) {
                // Store the audio file
                $audioFileName = 'audio_' . time() . '.mp3';
                $audioPath = 'audio/' . $audioFileName;
                
                Storage::disk('public')->put($audioPath, $response->body());
                
                return Storage::disk('public')->url($audioPath);
            } else {
                Log::error('Eleven Labs API error: ' . $response->body());
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Text to speech conversion failed: ' . $e->getMessage());
            return null;
        }
    }
} 