<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ComplaintController extends Controller
{
    /**
     * Display a listing of all complaints.
     */
    public function index()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        // FIXED: Use 'apartments.name' instead of 'apartments.property_name'
        $complaints = DB::table('complaints')
            ->leftJoin('users as tenants', 'complaints.tenant_id', '=', 'tenants.id')
            ->leftJoin('apartments', 'complaints.apartment_id', '=', 'apartments.id')
            ->select(
                'complaints.*',
                'tenants.name as tenant_name',
                'apartments.name as property_name'  // ← FIXED: changed from property_name to name
            )
            ->orderBy('complaints.created_at', 'desc')
            ->get()
            ->map(function ($complaint) {
                // Format the complaint object to match what the view expects
                return (object) [
                    'id' => $complaint->id,
                    'tenant' => $complaint->tenant_name ?? 'Unknown Tenant',
                    'property' => $complaint->property_name ?? 'Unknown Property',
                    'subject' => $complaint->subject,
                    'description' => $complaint->description,
                    'status' => $complaint->status ?? 'Pending',
                    'priority' => $complaint->priority ?? 'Normal',
                    'created_at' => $complaint->created_at,
                    'updated_at' => $complaint->updated_at,
                ];
            });

        // Get stats for dashboard cards - Updated status values to match database
        $stats = [
            'pending' => DB::table('complaints')->where('status', 'Pending')->count(),
            'urgent' => DB::table('complaints')->where('priority', 'Urgent')->count(),
            'resolved' => DB::table('complaints')->where('status', 'Resolved')->count(),
            'total' => DB::table('complaints')->count(),
        ];

        return view('admin.complaints_reports.index', compact('complaints', 'stats'));
    }

    /**
     * Mark a complaint as resolved.
     */
    public function resolve($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 401);
        }

        try {
            $complaint = DB::table('complaints')->where('id', $id)->first();

            if (!$complaint) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found.'
                ], 404);
            }

            DB::table('complaints')
                ->where('id', $id)
                ->update([
                    'status' => 'Resolved',
                    'resolved_at' => now(),
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "Ticket {$id} has been resolved.",
                'new_status' => 'Resolved'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Server Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new complaint.
     */
    public function store(Request $request)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'tenant_id' => 'required|exists:users,id',
            'apartment_id' => 'nullable|exists:apartments,id',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|in:Normal,Urgent',
        ]);

        $data = [
            'tenant_id' => $validated['tenant_id'],
            'apartment_id' => $validated['apartment_id'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('complaints')->insert($data);

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint submitted successfully.');
    }

    /**
     * Get complaint details for API.
     */
    public function show($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // FIXED: Use 'apartments.name' instead of 'apartments.property_name'
        $complaint = DB::table('complaints')
            ->leftJoin('users as tenants', 'complaints.tenant_id', '=', 'tenants.id')
            ->leftJoin('apartments', 'complaints.apartment_id', '=', 'apartments.id')
            ->select(
                'complaints.*',
                'tenants.name as tenant_name',
                'tenants.email as tenant_email',
                'tenants.phone as tenant_contact',
                'apartments.name as property_name'  // ← FIXED: changed from property_name to name
            )
            ->where('complaints.id', $id)
            ->first();

        if (!$complaint) {
            return response()->json(['error' => 'Not found'], 404);
        }

        return response()->json($complaint);
    }
}