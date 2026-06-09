<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PermitListController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        // CRITICAL FIX: Make sure we only get PENDING applications
        // Also verify the status values in database
        $applicants = DB::table('permit_applications')
            ->leftJoin('permit_numbers', 'permit_applications.permit_number', '=', 'permit_numbers.permit_number')
            ->where('permit_applications.status', 'pending')  // Only pending
            ->select(
                'permit_applications.*',
                'permit_numbers.owner_name as verified_owner_name',
                'permit_numbers.property_name as verified_property_name',
                'permit_numbers.status as permit_status',
                DB::raw('CASE WHEN permit_numbers.id IS NOT NULL AND permit_numbers.status = "active" THEN 1 ELSE 0 END as can_approve')
            )
            ->orderBy('permit_applications.created_at', 'desc')
            ->get();

        // Debug: Log the count and first record
        Log::info('Permit Verification Index Loaded', [
            'pending_count' => $applicants->count(),
            'first_status' => $applicants->first() ? $applicants->first()->status : 'none',
            'sql_check' => DB::table('permit_applications')->where('status', 'pending')->count()
        ]);

        $pendingCount = DB::table('permit_applications')->where('status', 'pending')->count();
        $approvedToday = DB::table('permit_applications')
            ->where('status', 'approved')
            ->whereDate('verified_at', today())
            ->count();
        $rejectedCount = DB::table('permit_applications')->where('status', 'rejected')->count();

        return view('admin.permit_verification.index', compact('applicants', 'pendingCount', 'approvedToday', 'rejectedCount'));
    }

    public function approve($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Log::info('Approve method called', ['application_id' => $id]);

        DB::beginTransaction();
        
        try {
            // First, get the application and check its current status
            $application = DB::table('permit_applications')->where('id', $id)->first();

            if (!$application) {
                DB::rollBack();
                Log::warning('Application not found', ['id' => $id]);
                return response()->json(['success' => false, 'message' => 'Application not found'], 404);
            }

            Log::info('Application found', [
                'id' => $application->id,
                'current_status' => $application->status,
                'permit_number' => $application->permit_number
            ]);

            // Check if already approved
            if ($application->status === 'approved') {
                DB::rollBack();
                Log::warning('Application already approved', ['id' => $id]);
                return response()->json([
                    'success' => false, 
                    'message' => 'This application has already been approved.'
                ], 400);
            }

            // IMPORTANT: Check if permit number exists in permit_numbers table
            $permitNumber = DB::table('permit_numbers')
                ->where('permit_number', $application->permit_number)
                ->first();

            if (!$permitNumber) {
                DB::rollBack();
                Log::warning('Permit number not found', ['permit_number' => $application->permit_number]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot approve: Permit number "' . $application->permit_number . '" is not registered in the system.'
                ], 400);
            }

            // Check if permit number is still active
            if ($permitNumber->status !== 'active') {
                DB::rollBack();
                Log::warning('Permit number not active', [
                    'permit_number' => $application->permit_number,
                    'status' => $permitNumber->status
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => 'Cannot approve: Permit number "' . $application->permit_number . '" is already used or inactive.'
                ], 400);
            }

            // UPDATE 1: Update application status to approved
            $updateResult = DB::table('permit_applications')
                ->where('id', $id)
                ->update([
                    'status' => 'approved',
                    'verified_at' => now(),
                    'updated_at' => now(),
                ]);

            Log::info('Application status updated', [
                'id' => $id,
                'rows_affected' => $updateResult,
                'new_status' => 'approved'
            ]);

            // Verify the update worked
            $verifyUpdate = DB::table('permit_applications')->where('id', $id)->first();
            Log::info('Verification after update', [
                'id' => $id,
                'status' => $verifyUpdate->status ?? 'not found'
            ]);

            // UPDATE 2: Update owner account and their apartments
            if ($application->user_id) {
                // Check if owner exists
                $ownerExists = DB::table('owners')->where('id', $application->user_id)->exists();
                
                if ($ownerExists) {
                    DB::table('owners')
                        ->where('id', $application->user_id)
                        ->update(['updated_at' => now()]);

                    // Update all apartments of this owner
                    DB::table('apartments')
                        ->where('owner_id', $application->user_id)
                        ->update([
                            'verification_status' => 'approved',
                            'verified_at' => now(),
                            'updated_at' => now(),
                        ]);
                    
                    Log::info('Owner and apartments updated', ['owner_id' => $application->user_id]);
                } else {
                    Log::warning('Owner not found in owners table', ['user_id' => $application->user_id]);
                }
            }

            // UPDATE 3: Mark permit number as used
            DB::table('permit_numbers')
                ->where('permit_number', $application->permit_number)
                ->update([
                    'status' => 'used',
                    'used_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::commit();

            // Get updated counts
            $newPendingCount = DB::table('permit_applications')->where('status', 'pending')->count();
            
            Log::info('Approval completed successfully', [
                'application_id' => $id,
                'new_pending_count' => $newPendingCount
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Application approved successfully!',
                'pending_count' => $newPendingCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Approval error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id
            ]);
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function reject($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        Log::info('Reject method called', ['application_id' => $id]);

        DB::beginTransaction();
        
        try {
            $application = DB::table('permit_applications')->where('id', $id)->first();

            if (!$application) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Application not found'], 404);
            }

            // Update application status to rejected
            DB::table('permit_applications')
                ->where('id', $id)
                ->update([
                    'status' => 'rejected',
                    'updated_at' => now(),
                ]);

            // Update owner account and their apartments
            if ($application->user_id) {
                $ownerExists = DB::table('owners')->where('id', $application->user_id)->exists();
                
                if ($ownerExists) {
                    DB::table('owners')
                        ->where('id', $application->user_id)
                        ->update(['updated_at' => now()]);

                    DB::table('apartments')
                        ->where('owner_id', $application->user_id)
                        ->update([
                            'verification_status' => 'rejected',
                            'rejection_reason' => 'Owner permit verification failed',
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();

            $newPendingCount = DB::table('permit_applications')->where('status', 'pending')->count();

            return response()->json([
                'success' => true, 
                'message' => 'Application rejected',
                'pending_count' => $newPendingCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rejection error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add a debug method to check database status
    public function debugStatus()
    {
        if (!session()->has('admin_email')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $pending = DB::table('permit_applications')->where('status', 'pending')->count();
        $approved = DB::table('permit_applications')->where('status', 'approved')->count();
        $rejected = DB::table('permit_applications')->where('status', 'rejected')->count();
        
        $allRecords = DB::table('permit_applications')
            ->select('id', 'status', 'permit_number', 'updated_at')
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'counts' => [
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected,
                'total' => $pending + $approved + $rejected
            ],
            'recent_records' => $allRecords
        ]);
    }

    // API: Validate permit number during registration
    public function validatePermitNumber(Request $request)
    {
        $request->validate([
            'permit_number' => 'required|string'
        ]);

        $permit = DB::table('permit_numbers')
            ->where('permit_number', $request->permit_number)
            ->where('status', 'active')
            ->first();

        if (!$permit) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid permit number. Please check and try again.'
            ], 404);
        }

        $existingApplication = DB::table('permit_applications')
            ->where('permit_number', $request->permit_number)
            ->first();

        if ($existingApplication) {
            return response()->json([
                'valid' => false,
                'message' => 'This permit number has already been used for registration.'
            ], 400);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Permit number is valid! You may now complete your registration.',
            'data' => [
                'permit_number' => $permit->permit_number,
                'owner_name' => $permit->owner_name,
                'property_name' => $permit->property_name,
            ]
        ]);
    }

    // Register owner using permit number
    public function registerOwner(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permit_number' => 'required|string',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'contact_number' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $permit = DB::table('permit_numbers')
            ->where('permit_number', $request->permit_number)
            ->where('status', 'active')
            ->first();

        if (!$permit) {
            return back()->with('error', 'Invalid permit number.');
        }

        $existingApplication = DB::table('permit_applications')
            ->where('permit_number', $request->permit_number)
            ->first();

        if ($existingApplication) {
            return back()->with('error', 'This permit number has already been used.');
        }

        DB::beginTransaction();
        
        try {
            $userId = DB::table('users')->insertGetId([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'contact_number' => $request->contact_number,
                'role' => 'owner',
                'status' => 'pending',
                'permit_number' => $request->permit_number,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('permit_applications')->insert([
                'permit_number' => $request->permit_number,
                'user_id' => $userId,
                'applicant_name' => $request->name,
                'email' => $request->email,
                'property_name' => $permit->property_name ?? 'N/A',
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::commit();
            
            return redirect()->route('login')->with('success', 
                'Registration successful! Your account is pending approval.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration error: ' . $e->getMessage());
            return back()->with('error', 'Registration failed. Please try again.');
        }
    }
}