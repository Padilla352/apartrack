<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Exception;

class GoogleController extends Controller
{
    // Redirect to Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Handle Google callback
    public function callback()
    {
        try {
            // IMPORTANT: NO stateless() for normal Laravel session apps
            $googleUser = Socialite::driver('google')->user();

            // Find user by email
            $user = User::where('email', $googleUser->email)->first();

            // Create user if not exists
            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->name ?? 'Google User',
                    'email' => $googleUser->email,
                    'password' => bcrypt(Str::random(32)),
                ]);
            }

            // Login user
            Auth::login($user);

            return redirect('/dashboard');

        } catch (Exception $e) {
            // Show real error for debugging
            return redirect('/login')->with('error', $e->getMessage());
        }
    }
}