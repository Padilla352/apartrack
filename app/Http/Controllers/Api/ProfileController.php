<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Get authenticated user profile (JSON)
     */
    public function show()
    {
        return response()->json(auth()->user()->only('id', 'name', 'email', 'phone', 'address', 'avatar', 'created_at'));
    }

    /**
     * Update profile (name, phone, address)
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'    => 'required|string|max:255|regex:/^[A-Za-z\s\-]+$/',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json([
            'success' => true,
            'user'    => $user->fresh()->only('name', 'phone', 'address')
        ]);
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password updated']);
    }

    /**
     * Upload avatar
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = auth()->user();
        $path = $request->file('avatar')->store('avatars', 'public');

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->avatar = $path;
        $user->save();

        return response()->json([
            'success'    => true,
            'avatar_url' => Storage::url($path),
        ]);
    }

    /**
     * Delete account
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = auth()->user();
        Auth::logout();
        $user->delete();

        return response()->json(['message' => 'Account deleted']);
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = auth()->user();
        $validated = $request->validate([
            'maintenance_notifications'  => 'sometimes|boolean',
            'announcement_notifications' => 'sometimes|boolean',
            'email_notifications'        => 'sometimes|boolean',
            'push_notifications'         => 'sometimes|boolean',
        ]);

        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }
        $user->save();

        return response()->json(['success' => true]);
    }
}