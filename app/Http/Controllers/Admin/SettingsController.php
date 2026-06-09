<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingsController extends Controller
{
    /**
     * Ipakita ang Settings Page (Profile at Security)
     */
    public function index()
    {
        // Kinukuha ang email mula sa session na itinakda sa Login
        $adminEmail = session('admin_email');

        // Check kung logged in (may session email)
        if (!$adminEmail) {
            return redirect()->route('login')->with('error', 'Please login first.');
        }

        // Kunin ang admin data gamit ang DB facade
        $admin = DB::table('admins')->where('email', $adminEmail)->first();

        // Safety check kung sakaling nabura ang admin sa DB habang naka-session pa
        if (!$admin) {
            session()->forget('admin_email');
            return redirect()->route('login')->with('error', 'Account not found.');
        }

        return view('admin.settings', compact('admin'));
    }

    /**
     * Update Profile Info (Name, Email, Photo)
     */
    public function updateProfile(Request $request)
    {
        $currentEmail = session('admin_email');
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admins,email,' . $currentEmail . ',email',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'updated_at' => now(),
            ];

            // Handle Image Upload
            if ($request->hasFile('profile_image')) {
                // Kunin ang lumang photo para burahin (para hindi mapuno ang storage)
                $oldAdmin = DB::table('admins')->where('email', $currentEmail)->first();
                if ($oldAdmin && $oldAdmin->profile_image) {
                    Storage::disk('public')->delete($oldAdmin->profile_image);
                }

                $path = $request->file('profile_image')->store('profile_photos', 'public');
                $data['profile_image'] = $path;
            }

            DB::table('admins')->where('email', $currentEmail)->update($data);
            
            // I-update ang session email kung sakaling binago ng admin ang email niya
            session(['admin_email' => $request->email]);

            return redirect()->back()->with('success', 'Profile updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    /**
     * Update Password (Security Tab)
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed', // 'confirmed' means need ng 'new_password_confirmation' field
        ]);

        $adminEmail = session('admin_email');
        $admin = DB::table('admins')->where('email', $adminEmail)->first();

        // 1. Verify Current Password
        // Ginagamit ang Hash::check para i-verify ang Bcrypt password na ininsert natin sa Tinker
        if (!Hash::check($request->current_password, $admin->password)) {
            return redirect()->back()->with('error', 'Ang iyong kasalukuyang password ay mali.');
        }

        // 2. Update to New Password (Hashed)
        try {
            DB::table('admins')->where('email', $adminEmail)->update([
                'password' => Hash::make($request->new_password),
                'updated_at' => now(),
            ]);

            return redirect()->back()->with('success', 'Password updated successfully!');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating password.');
        }
    }
}