<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            'email'                 => 'required|string|email|max:255|unique:users',
            'phone'                 => 'required|string|max:15|regex:/^[0-9+\-\s]+$/|min:10',
            'address'               => 'required|string|max:500',
            'password'              => 'required|string|min:8|confirmed',
        ], [
            'name.regex'            => 'Full name may only contain letters and spaces.',
            'phone.regex'           => 'Phone number may only contain digits, spaces, +, or -.',
            'phone.min'             => 'Phone number must be at least 10 digits.',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'address'   => $validated['address'],
            'password'  => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }
}