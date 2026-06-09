<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Owner\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     */
    public function index()
    {
        $owner = Auth::guard('owner')->user();
        return view('owner.profile', compact('owner'));
    }
    
    /**
     * Update profile information.
     */
    public function updateProfile(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        
        if (!$owner) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update profile'
            ], 401);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners,email,' . $owner->id,
            'phone' => 'nullable|string|max:20',
            'birthdate' => 'nullable|date|before:today',  // ADDED: birthdate validation
            'address' => 'nullable|string|max:500',
            'permit_number' => 'nullable|string|max:100',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);
        
        // Update basic info
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->phone = $request->phone;
        $owner->address = $request->address;
        $owner->permit_number = $request->permit_number;
        
        // Update birthdate if provided
        if ($request->filled('birthdate')) {
            $owner->birthdate = $request->birthdate;
        }
        
        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $file = $request->file('profile_photo');
            $filename = 'profile_' . $owner->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            // Delete old photo if exists
            if ($owner->profile_photo && Storage::disk('public')->exists($owner->profile_photo)) {
                Storage::disk('public')->delete($owner->profile_photo);
            }
            
            // Store new photo
            $path = $file->storeAs('profile-photos', $filename, 'public');
            $owner->profile_photo = $path;
        }
        
        $owner->save();
        
        // Calculate age for response
        $age = $owner->birthdate ? Carbon::parse($owner->birthdate)->age . ' yrs old' : 'Not set';
        
        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!',
            'photo_url' => $owner->profile_photo ? asset('storage/' . $owner->profile_photo) : null,
            'age' => $age,
            'formatted_birthdate' => $owner->birthdate ? Carbon::parse($owner->birthdate)->format('F d, Y') : 'Not set'
        ]);
    }
    
    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $owner = Auth::guard('owner')->user();
        
        if (!$owner) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to update password'
            ], 401);
        }
        
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|max:20',
            'confirm_password' => 'required|same:new_password'
        ]);
        
        // Check current password
        if (!Hash::check($request->current_password, $owner->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect!'
            ], 422);
        }
        
        // Check if new password is same as current
        if (Hash::check($request->new_password, $owner->password)) {
            return response()->json([
                'success' => false,
                'message' => 'New password must be different from current password!'
            ], 422);
        }
        
        // Update password
        $owner->password = Hash::make($request->new_password);
        $owner->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully!'
        ]);
    }
    
    /**
     * Get profile data as JSON.
     */
    public function getProfileData()
    {
        $owner = Auth::guard('owner')->user();
        
        if (!$owner) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'name' => $owner->name,
                'email' => $owner->email,
                'phone' => $owner->phone,
                'age' => $owner->age,
                'birthdate' => $owner->birthdate,
                'formatted_birthdate' => $owner->formatted_birthdate,
                'permit_number' => $owner->permit_number,
                'address' => $owner->address,
                'role' => $owner->role ?? 'Owner',
                'is_active' => $owner->is_active,
                'profile_photo_url' => $owner->profile_photo_url,
                'joined_date' => $owner->created_at ? $owner->created_at->format('F d, Y') : 'N/A'
            ]
        ]);
    }
}