<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\ClientException;

class SocialiteController extends Controller
{
    // ========== FACEBOOK ==========
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->stateless()->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $fbUser = Socialite::driver('facebook')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $fbUser->getEmail()],
                [
                    'name' => $fbUser->getName(),
                    'facebook_id' => $fbUser->getId(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(32)),
                ]
            );

            Auth::login($user);
            return redirect()->route('home');
        } catch (ClientException $e) {
            // Authorization code expired or invalid – restart the login flow
            return redirect()->route('facebook.login')
                ->with('error', 'Facebook login expired or invalid. Please try again.');
        }
    }

    // ========== GOOGLE ==========
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'email_verified_at' => now(),
                    'password' => bcrypt(Str::random(32)),
                ]
            );

            Auth::login($user);
            return redirect()->route('home');
        } catch (ClientException $e) {
            // Google codes also expire – redirect back to start
            return redirect()->route('google.login')
                ->with('error', 'Google login expired or invalid. Please try again.');
        }
    }
}