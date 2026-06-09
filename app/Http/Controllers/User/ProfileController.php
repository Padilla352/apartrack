<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile (read-only).
     */
    public function show()
    {
        if (Auth::guard('owner')->check()) {
            return redirect()->route('owner.profile');
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Show the settings page.
     */
    public function settings()
    {
        if (Auth::guard('owner')->check()) {
            return redirect()->route('owner.settings');
        }
        return view('user.settings');
    }

    /**
     * Update profile from settings page (name, phone, address).
     * Route: settings.profile (PUT)
     */
    public function updateSettingsProfile(Request $request)
    {
        $request->validate([
            // Allow letters, spaces, hyphens and apostrophes (e.g. O'Connor)
            'name'    => "required|string|max:255|regex:/^[A-Za-z\\s\\-']+$/",
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->name    = $request->name;
        $user->phone   = $request->phone;
        $user->address = $request->address;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully',
            'user'    => $user->fresh()->only('name', 'phone', 'address')
        ]);
    }

    /**
     * Update avatar from settings page.
     * Route: settings.avatar (POST)
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Delete old avatar if exists
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success'    => true,
            'avatar_url' => Storage::url($path),
            'message'    => 'Avatar updated successfully.'
        ]);
    }

    /**
     * Update password from settings page.
     * Route: settings.password (PUT)
     */
    public function updateSettingsPassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password'     => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    /**
     * Update notification preferences.
     * Route: settings.notifications (POST)
     */
    public function updateNotifications(Request $request)
    {
        $validated = $request->validate([
            'maintenance_notifications'  => 'sometimes|boolean',
            'announcement_notifications' => 'sometimes|boolean',
            'email_notifications'        => 'sometimes|boolean',
            'push_notifications'         => 'sometimes|boolean',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }
        $user->save();

        return response()->json(['success' => true]);
    }

    /**
     * Logout from all devices (invalidate all sessions).
     * Route: logout.all (POST) – returns redirect.
     */
    public function logoutAllDevices(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Delete all session records for this user
        DB::table('sessions')->where('user_id', $user->id)->delete();

        // If using Laravel Sanctum, also delete tokens:
        // $user->tokens()->delete();

        // Logout current user
        Auth::logout();

        // Invalidate session and regenerate token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out from all devices.');
    }

    /**
     * Delete user account (with password confirmation).
     * Route: profile.destroy (DELETE)
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}