<?php

namespace App\Http\Controllers\Owner;

use App\Models\Apartment;
use App\Models\Tenant;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $query = Tenant::with('apartment');
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by apartment
        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }
        
        // Get paginated results
        $tenants = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Statistics for cards
        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'Active')->count(),
            'expiring_soon' => Tenant::where('status', 'Active')
                ->whereNotNull('lease_end_date')
                ->whereDate('lease_end_date', '<=', now()->addDays(30))
                ->whereDate('lease_end_date', '>=', now())
                ->count(),
            'pending' => Tenant::where('status', 'Pending')->count()
        ];
        
        // Get all apartments for filter dropdown
        $apartments = Apartment::orderBy('unit_number')->get();
        
        // Get vacant apartments for quick add modal
        $vacantApartments = Apartment::where('status', 'Vacant')->orderBy('unit_number')->get();
        
        return view('owner.tenants.index', compact('tenants', 'stats', 'apartments', 'vacantApartments'));
    }
    
    public function create()
    {
        $apartments = Apartment::where('status', 'Vacant')->get();
        $barangays = Barangay::orderBy('name')->get();
        return view('owner.tenants.create', compact('apartments', 'barangays'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'apartment_id' => 'nullable|exists:apartments,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants',
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'barangay_id' => 'nullable|exists:barangays,id',
            'move_in_date' => 'required|date',
            'lease_end_date' => 'nullable|date|after:move_in_date',
            'security_deposit' => 'nullable|numeric|min:0',
            'monthly_rent' => 'nullable|numeric|min:0',
            'emergency_contact' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            $validated['status'] = 'Active';
            
            $tenant = Tenant::create($validated);
            
            DB::commit();
            
            return redirect()->route('owner.tenants.show', $tenant->id)
                ->with('success', 'Tenant added successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to add tenant. Error: ' . $e->getMessage());
        }
    }
    
    public function show(Tenant $tenant)
    {
        $tenant->load('apartment', 'barangay');
        return view('owner.tenants.show', compact('tenant'));
    }
    
    public function edit(Tenant $tenant)
    {
        $apartments = Apartment::all();
        $barangays = Barangay::orderBy('name')->get();
        return view('owner.tenants.edit', compact('tenant', 'apartments', 'barangays'));
    }
    
    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'apartment_id' => 'nullable|exists:apartments,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'required|string|max:20',
            'alternate_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'barangay_id' => 'nullable|exists:barangays,id',
            'move_in_date' => 'required|date',
            'lease_end_date' => 'nullable|date|after:move_in_date',
            'security_deposit' => 'nullable|numeric|min:0',
            'monthly_rent' => 'nullable|numeric|min:0',
            'status' => 'required|in:Active,Inactive,Pending,Evicted',
            'emergency_contact' => 'nullable|array',
            'notes' => 'nullable|string'
        ]);
        
        try {
            DB::beginTransaction();
            
            $oldApartmentId = $tenant->apartment_id;
            $tenant->update($validated);
            
            // Update apartment status if changed
            if ($oldApartmentId != $validated['apartment_id']) {
                if ($oldApartmentId) {
                    Apartment::find($oldApartmentId)?->updateStatus();
                }
                if ($validated['apartment_id']) {
                    Apartment::find($validated['apartment_id'])?->updateStatus();
                }
            }
            
            DB::commit();
            
            return redirect()->route('owner.tenants.show', $tenant->id)
                ->with('success', 'Tenant updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to update tenant. Error: ' . $e->getMessage());
        }
    }
    
    public function destroy(Tenant $tenant)
    {
        try {
            DB::beginTransaction();
            
            $tenant->delete();
            
            DB::commit();
            
            return redirect()->route('owner.tenants.index')
                ->with('success', 'Tenant deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('owner.tenants.index')
                ->with('error', 'Failed to delete tenant. Error: ' . $e->getMessage());
        }
    }
}