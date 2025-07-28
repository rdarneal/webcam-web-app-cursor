<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle(): JsonResponse
    {
        $redirectUrl = Socialite::driver('google')->redirect()->getTargetUrl();
        
        return response()->json([
            'success' => true,
            'redirect_url' => $redirectUrl
        ]);
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find or create user
            $user = User::where('email', $googleUser->getEmail())->first();
            
            if ($user) {
                // Update existing user with Google info if not already set
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Create new user
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => null, // No password for social login
                ]);
            }

            // Log the user in
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            // Return HTML that closes popup and notifies parent
            return view('auth.google-success', [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'has_openai_key' => $user->hasApiKey('openai'),
                    'has_elevenlabs_key' => $user->hasApiKey('elevenlabs'),
                ]
            ]);

        } catch (\Exception $e) {
            // Return HTML that closes popup and notifies parent of error
            return view('auth.google-error', [
                'error' => $e->getMessage()
            ]);
        }
    }
} 