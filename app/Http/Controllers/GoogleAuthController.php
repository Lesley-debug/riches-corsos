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
                'email' => 'Google Sign-In is currently being set up. Please sign in with your email or password.',
            ]);
        }

        try {
            return Socialite::driver('google')->redirect();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Unable to connect to Google: ' . $e->getMessage(),
            ]);
        }
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'Google authentication was cancelled or encountered an error. Please try again.',
            ]);
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $user->google_id ?? $googleUser->getId(),
                'avatar' => $user->avatar ?? $googleUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Corso Enthusiast',
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'password' => Hash::make(Str::random(32)),
                'role' => 'customer',
            ]);

            // Dispatch welcome notification & email for new user
            $user->notify(new WelcomeNotification());
        }

        Auth::login($user, true);

        return redirect()->route('account.dashboard')->with('success', 'Successfully signed in with Google.');
    }
}
