<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    use ApiResponse;

    public function SocialLogin(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'provider' => 'required|in:google,facebook,apple',
        ]);

        try {
            $provider = $request->provider;

            // Optional: Token validation logic for JWT if necessary
            // Google tokens usually have 2 dots, but not always required to verify manually here
            // Socialite's userFromToken() handles the heaviest lifting.

            $socialUser = Socialite::driver($provider)->stateless()->userFromToken($request->token);

            if (! $socialUser || ! $socialUser->getEmail()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to retrieve user from provider.',
                    'code' => 400,
                ], 400);
            }

            $user = User::where('email', $socialUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'name' => $socialUser->getName() ?? ucfirst($provider).' User',
                    'email' => $socialUser->getEmail(),
                    'password' => bcrypt(Str::random(16)),
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                ]);
            } else {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $socialUser->getId(),
                    'avatar' => $socialUser->getAvatar(),
                ]);
            }

            Auth::login($user);
            $token = $user->createToken('YourAppName')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login successful.',
                'token_type' => 'bearer',
                'token' => $token,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
                'code' => 500,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
