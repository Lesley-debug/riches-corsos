<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\WelcomeNotification;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return redirect()->route('login')->withErrors([
                'google' => 'Google Client ID and Secret are missing in .env. Please configure GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET.',
            ]);
        }

        try {
            return Socialite::driver('google')
                ->redirect();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'google' => 'Unable to connect to Google: ' . $e->getMessage(),
            ]);
        }
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            try {
                $googleProvider = Socialite::driver('google');

                if (!method_exists($googleProvider, 'stateless')) {
                    throw new Exception('Stateless Google authentication is unavailable.');
                }

                $googleUser = $googleProvider->stateless()->user();
            } catch (Exception $e2) {
                return redirect()->route('login')->withErrors([
                    'google' => 'Google authentication was cancelled or encountered an error. Please try again.',
                ]);
            }
        }

        $email = $googleUser->getEmail();
        $googleId = $googleUser->getId();
        $avatar = $googleUser->getAvatar();
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Corso Client');

        if (empty($email)) {
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to retrieve your email address from Google. Please sign in with email.',
            ]);
        }

        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $user->google_id ?? $googleId,
                'avatar' => $avatar ?? $user->avatar,
                'name' => $user->name ?: $name,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $avatar,
                'password' => Hash::make(Str::random(32)),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]);

            // Dispatch welcome notification & email for new user
            $user->notify(new WelcomeNotification());
        }

        Auth::login($user, true);

        return redirect()->route('home')->with('login_notice', 'Welcome! You can visit your Account Dashboard anytime to track your puppy reservations and site activity.');
    }
}
