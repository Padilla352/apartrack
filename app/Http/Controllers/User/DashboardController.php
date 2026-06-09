<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\Controller; 

class DashboardController extends Controller
{
    /**
     * Dashboard para sa mga regular Tenants/Users.
     */
    public function index()
    {
        return view('user.dashboard');
    }

    /**
     * Dashboard para sa mga Apartment Owners.
     */
    public function ownerDashboard()
    {
        // 1. Prepare Stats (Replace these with real DB counts later)
        // Example: 'total_tenants' => \App\Models\Tenant::where('owner_id', Auth::id())->count()
        $stats = [
            'total_tenants' => 0,
            'pending_tenants' => 0,
            'pending' => 0,
            'maintenance' => 0,
            'approved' => 0,
            'occupancy_rate' => 0,
            'reports' => 0,
            'recent_reports' => 0,
        ];

        // 2. Prepare Monthly Data for the Graph
        $monthlyTenantData = [
            ['month' => 'Jan', 'count' => 5], ['month' => 'Feb', 'count' => 10],
            ['month' => 'Mar', 'count' => 8], ['month' => 'Apr', 'count' => 12],
            ['month' => 'May', 'count' => 15], ['month' => 'Jun', 'count' => 20],
            ['month' => 'Jul', 'count' => 18], ['month' => 'Aug', 'count' => 22],
            ['month' => 'Sep', 'count' => 25], ['month' => 'Oct', 'count' => 23],
            ['month' => 'Nov', 'count' => 28], ['month' => 'Dec', 'count' => 30]
        ];

        // 3. Get Recent Tenants (Empty collection for now to avoid errors)
        $recentTenants = collect(); 

        return view('owner.dashboard', compact('stats', 'monthlyTenantData', 'recentTenants'));
    }

    /**
     * Listahan ng mga tenants para sa owner dashboard.
     */
    public function tenantsList()
    {
        return view('owner.tenants.index');
    }

    /**
     * Detalye ng isang specific na tenant.
     */
    public function showTenant($id)
    {
        return view('owner.tenants.show', compact('id'));
    }

    /**
     * Reports at complaints view.
     * Routes to the appropriate reports view based on user role.
     */
    public function reports()
    {
        // Check if user is owner
        if (Auth::guard('owner')->check()) {
            // Forward to Owner's DashboardController reports method
            return app(\App\Http\Controllers\Owner\DashboardController::class)->reports();
        }
        
        // Fallback for non-owner users (regular tenants)
        // You can change this to a different view if needed
        return view('reports.tenants');
    }

    /**
     * Owner Profile view.
     */
    public function profile()
    {
        return view('owner.profile');
    }

    /**
     * Owner Settings view.
     */
    public function settings()
    {
        return view('owner.settings');
    }

    /**
     * Update Owner Password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password successfully updated!');
    }

    /**
     * Kunin ang mga pinakabagong aktibidad para sa Dashboard.
     */
    public function getRecentActivities()
    {
        return response()->json([
            'activities' => []
        ]);
    }
}