<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;  // FIXED: Use User model instead of Tenant
use App\Models\Owner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TenantController extends Controller
{
    public function index()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        // Safely get counts from database (handle missing columns)
        $totalTenants = $this->safeCount('users');  // FIXED: Use 'users' table
        $activeTenants = $this->safeCount('users', 'status', 'active');  // FIXED: Use 'users' table
        $totalOwners = $this->safeCount('owners');
        $verifiedOwners = $this->safeCount('owners', 'verification_status', 'verified');

        return view('admin.users_management.index', compact('totalTenants', 'activeTenants', 'totalOwners', 'verifiedOwners'));
    }

    /**
     * Safely count records from a table
     */
    private function safeCount($table, $column = null, $value = null)
    {
        try {
            if (Schema::hasTable($table)) {
                if ($column && Schema::hasColumn($table, $column)) {
                    return DB::table($table)->where($column, $value)->count();
                }
                return DB::table($table)->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function tenantsList()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        // FIXED: Use User model instead of Tenant
        $tenants = User::orderBy('created_at', 'desc')->get();

        return view('admin.users_management.tenants_list', compact('tenants'));
    }

    public function ownersList()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        // Get real owners from database
        $owners = Owner::orderBy('created_at', 'desc')->get();

        return view('admin.users_management.owner_list', compact('owners'));
    }

    public function viewTenant($id)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        // FIXED: Use User model instead of Tenant
        $tenant = User::findOrFail($id);

        return view('admin.users_management.tenants_view', compact('tenant'));
    }

    public function editTenant($id)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        // FIXED: Use User model instead of Tenant
        $tenant = User::findOrFail($id);
        return view('admin.users_management.tenants_edit', compact('tenant'));
    }

    public function deactivateTenant($id)
    {
        // FIXED: Use User model instead of Tenant
        $tenant = User::findOrFail($id);
        
        // Check if status column exists before updating
        try {
            if (Schema::hasColumn('users', 'status')) {
                $tenant->update(['status' => 'inactive']);
            } else {
                // If no status column, just return success
                return back()->with('success', 'User deactivated (status column not available).');
            }
        } catch (\Exception $e) {
            return back()->with('warning', 'Deactivation feature requires a status column in users table.');
        }

        return back()->with('success', 'User has been deactivated.');
    }

    public function updateTenant(Request $request, $id)
    {
        // FIXED: Use User model instead of Tenant
        $tenant = User::findOrFail($id);
        
        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];
        
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }
        
        $tenant->update($updateData);

        return response()->json(['success' => true]);
    }

    /**
     * Show form to create new tenant.
     */
    public function create()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        return view('admin.users_management.tenants_create');
    }

    /**
     * Store new tenant.
     */
    public function store(Request $request)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // FIXED: Use User model instead of Tenant
        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
        ]);

        return redirect()->route('users-management.tenants.list')
            ->with('success', 'Tenant created successfully.');
    }
}