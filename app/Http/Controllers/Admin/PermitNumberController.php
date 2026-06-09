<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PermitNumberController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        $permits = DB::table('permit_numbers')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.permit_numbers.index', compact('permits'));
    }

    public function store(Request $request)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        $request->validate([
            'permit_number' => 'required|unique:permit_numbers',
            'permit_type' => 'required|in:residential,business',
            'owner_name' => 'required|string|max:255',
            'property_name' => 'nullable|string|max:255',
        ]);

        $insertData = [
            'permit_number' => $request->permit_number,
            'permit_type' => $request->permit_type,
            'owner_name' => $request->owner_name,
            'property_name' => $request->property_name ?? 'N/A',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('permit_numbers')->insert($insertData);

        return back()->with('success', 'Permit number added successfully!');
    }

    /**
     * Delete permit number with admin password verification
     */
    public function destroy(Request $request, $id)
    {
        // Check if admin is logged in
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized. Please login as admin.'], 401);
        }

        // Validate that password is provided
        $request->validate([
            'admin_password' => 'required|string|min:1'
        ]);

        // Get admin email from session
        $adminEmail = session('admin_email');
        
        // Fetch admin from database (assuming 'admins' table exists)
        $admin = DB::table('admins')->where('email', $adminEmail)->first();

        if (!$admin) {
            return response()->json([
                'success' => false, 
                'message' => 'Admin account not found.'
            ], 404);
        }

        // Verify admin password
        $passwordValid = false;
        
        // Check if password is hashed (Laravel Hash) or plain text
        if (password_get_info($admin->password)['algo'] !== 0) {
            // Password is hashed using Hash::make()
            $passwordValid = Hash::check($request->admin_password, $admin->password);
        } else {
            // Password is plain text (less secure, but backward compatible)
            $passwordValid = ($request->admin_password === $admin->password);
        }

        if (!$passwordValid) {
            // Log failed attempt for security
            Log::warning('Failed permit deletion attempt', [
                'admin_email' => $adminEmail,
                'permit_id' => $id,
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => false, 
                'message' => 'Invalid admin password. Deletion cancelled.'
            ], 403);
        }

        // Check if permit exists
        $permit = DB::table('permit_numbers')->where('id', $id)->first();
        
        if (!$permit) {
            return response()->json([
                'success' => false, 
                'message' => 'Permit number not found.'
            ], 404);
        }

        // Only allow deletion of active permits (prevent deleting used ones)
        if ($permit->status !== 'active') {
            return response()->json([
                'success' => false, 
                'message' => 'Cannot delete a permit number that has already been used.'
            ], 403);
        }

        // Delete the permit number
        DB::table('permit_numbers')->where('id', $id)->delete();

        // Log successful deletion for audit trail
        Log::info('Permit number deleted successfully', [
            'permit_number' => $permit->permit_number,
            'permit_id' => $id,
            'deleted_by' => $adminEmail,
            'deleted_at' => now()
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Permit number deleted successfully.'
        ]);
    }
}