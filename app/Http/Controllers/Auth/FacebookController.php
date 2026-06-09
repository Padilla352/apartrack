<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

class FacebookController extends Controller
{
    /**
     * Redirect user to Facebook login page
     */
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Handle callback from Facebook
     */
    public function callback()
    {
        try {
            // Get user data from Facebook
            $fbUser = Socialite::driver('facebook')->user();

            // Fallback email if Facebook didn't provide one
            $email = $fbUser->getEmail();
            if (!$email) {
                $email = $fbUser->getId() . '@facebook.com';
            }

            // Create or update user (with avatar)
            $user = User::updateOrCreate(
                ['email' => $email], // search by email
                [
                    'name'        => $fbUser->getName() ?? $fbUser->getNickname() ?? 'Facebook User',
                    'facebook_id' => $fbUser->getId(),
                    'avatar'      => $fbUser->getAvatar(), // ✅ store Facebook avatar URL
                    'password'    => bcrypt(str()->random(24)), // random password for OAuth users
                ]
            );

            // Log the user in
            Auth::login($user);

            // Redirect to intended page or dashboard
            return redirect()->intended('/dashboard');

        } catch (Exception $e) {
            // Something went wrong, redirect back to login with error
            return redirect('/login')->with('error', 'Facebook login failed: ' . $e->getMessage());
        }
    }
}