<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        return view('user.settings');
    }

    // Update profile (name, phone, address)
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|regex:/^[A-Za-z\s\-\']+$/|max:255',
            'phone'   => 'nullable|string|regex:/^[\+0-9\s\-\(\)]+$/|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $user->name    = $request->name;
        $user->phone   = $request->phone;
        $user->address = $request->address;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.'
        ]);
    }

    // Change password
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'
        ]);
    }

    // Upload avatar via AJAX (returns JSON)
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user = Auth::user();

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

    // Update notification preferences
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $fields = ['maintenance_notifications', 'announcement_notifications', 'email_notifications', 'push_notifications'];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                $user->$field = filter_var($request->$field, FILTER_VALIDATE_BOOLEAN);
            }
        }
        $user->save();

        return response()->json(['success' => true]);
    }

    // Logout from all other devices
    public function logoutAllDevices(Request $request)
    {
        $request->validate([
            'password' => 'required|current_password'
        ]);

        Auth::logoutOtherDevices($request->password);

        return back()->with('success', 'Logged out from all other devices.');
    }

    // Delete account
    public function destroy(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}