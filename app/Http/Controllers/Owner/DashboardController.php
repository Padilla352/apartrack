<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Tenant;
use App\Models\Owner;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $ownerId = Auth::guard('owner')->id();

        // Real statistics - Now using owner_id from apartments table
        $totalApartments = Apartment::where('owner_id', $ownerId)->count();
        $occupiedUnits = Apartment::where('owner_id', $ownerId)
            ->where('status', 'Occupied')
            ->count();
        $vacantUnits = Apartment::where('owner_id', $ownerId)
            ->where('status', 'Vacant')
            ->count();
        $maintenanceUnits = Apartment::where('owner_id', $ownerId)
            ->where('status', 'Maintenance')
            ->count();

        $totalTenants = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->count();

        $pendingTenants = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'Pending')->count();

        $occupancyRate = $totalApartments > 0 ? round(($occupiedUnits / $totalApartments) * 100) : 0;

        // Monthly tenant data for graph
        $monthlyTenantData = [];
        for ($month = 1; $month <= 12; $month++) {
            $count = Tenant::whereHas('apartment', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $monthlyTenantData[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'count' => $count
            ];
        }

        // Recent tenants for email list
        $recentTenants = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
            ->with('apartment')
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'total_tenants' => $totalTenants,
            'pending_tenants' => $pendingTenants,
            'pending' => $vacantUnits,
            'approved' => $occupiedUnits,
            'occupancy_rate' => $occupancyRate,
            'reports' => 0,
            'recent_reports' => 0,
            'maintenance' => $maintenanceUnits
        ];

        return view('owner.dashboard', compact('stats', 'monthlyTenantData', 'recentTenants'));
    }

    /**
     * Display tenants list with filters.
     */
    public function tenantsList(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Get apartments for filter dropdown
        $apartments = Apartment::where('owner_id', $ownerId)->get();
        
        // Build query
        $query = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->with('apartment');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('apartment_id')) {
            $query->where('apartment_id', $request->apartment_id);
        }
        
        $tenants = $query->latest()->paginate(15);
        
        // Statistics
        $stats = [
            'total' => Tenant::whereHas('apartment', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->count(),
            'active' => Tenant::whereHas('apartment', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->where('status', 'Active')->count(),
            'expiring_soon' => Tenant::whereHas('apartment', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->where('lease_end_date', '<=', Carbon::now()->addDays(30))
              ->where('lease_end_date', '>=', Carbon::now())
              ->count(),
            'pending' => Tenant::whereHas('apartment', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->where('status', 'Pending')->count(),
        ];
        
        return view('owner.tenants.index', compact('tenants', 'stats', 'apartments'));
    }

    /**
     * Show form to create a new tenant.
     */
    public function createTenant()
    {
        $ownerId = Auth::guard('owner')->id();
        $apartments = Apartment::where('owner_id', $ownerId)->where('status', 'Vacant')->get();
        return view('owner.tenants.create', compact('apartments'));
    }

    /**
     * Store a new tenant.
     */
    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:20',
            'apartment_id' => 'nullable|exists:apartments,id',
            'move_in_date' => 'nullable|date',
            'lease_end_date' => 'nullable|date|after:move_in_date',
            'status' => 'required|in:Active,Pending,Inactive,Evicted',
        ]);

        $tenant = Tenant::create($validated);
        
        // Update apartment status if tenant is assigned
        if ($request->apartment_id) {
            Apartment::where('id', $request->apartment_id)->update(['status' => 'Occupied']);
        }

        return redirect()->route('owner.tenants.index')
            ->with('success', 'Tenant added successfully.');
    }

    /**
     * Display a specific tenant.
     */
    public function showTenant($id)
    {
        $ownerId = Auth::guard('owner')->id();
        
        $tenant = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->with('apartment')->findOrFail($id);
        
        return view('owner.tenants.show', compact('tenant'));
    }

    /**
     * Show form to edit a tenant.
     */
    public function editTenant($id)
    {
        $ownerId = Auth::guard('owner')->id();
        
        $tenant = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->findOrFail($id);
        
        $apartments = Apartment::where('owner_id', $ownerId)->get();
        
        return view('owner.tenants.edit', compact('tenant', 'apartments'));
    }

    /**
     * Update a tenant.
     */
    public function updateTenant(Request $request, $id)
    {
        $ownerId = Auth::guard('owner')->id();
        
        $tenant = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->findOrFail($id);
        
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'apartment_id' => 'nullable|exists:apartments,id',
            'move_in_date' => 'nullable|date',
            'lease_end_date' => 'nullable|date|after:move_in_date',
            'status' => 'required|in:Active,Pending,Inactive,Evicted',
        ]);

        // Handle apartment change
        if ($tenant->apartment_id != $request->apartment_id) {
            // Make previous apartment vacant
            if ($tenant->apartment_id) {
                Apartment::where('id', $tenant->apartment_id)->update(['status' => 'Vacant']);
            }
            // Make new apartment occupied
            if ($request->apartment_id) {
                Apartment::where('id', $request->apartment_id)->update(['status' => 'Occupied']);
            }
        }

        $tenant->update($validated);

        return redirect()->route('owner.tenants.index')
            ->with('success', 'Tenant updated successfully.');
    }

    /**
     * Delete a tenant.
     */
    public function destroyTenant($id)
    {
        $ownerId = Auth::guard('owner')->id();
        
        $tenant = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->findOrFail($id);
        
        // Make apartment vacant
        if ($tenant->apartment_id) {
            Apartment::where('id', $tenant->apartment_id)->update(['status' => 'Vacant']);
        }
        
        $tenant->delete();

        return redirect()->route('owner.tenants.index')
            ->with('success', 'Tenant deleted successfully.');
    }

    /**
     * Display tenants report using existing owner.reports.tenants view.
     */
    public function reports()
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Get tenants associated with this owner's apartments
        $tenants = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
        ->with('apartment')
        ->latest()
        ->paginate(15);
        
        // Calculate total tenants
        $totalTenants = $tenants->total();
        
        // Calculate active tenants (adjust status based on your database)
        $activeTenants = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })->where('status', 'Active')->count();
        
        // Calculate inactive tenants
        $inactiveTenants = $totalTenants - $activeTenants;
        
        // Tenants by Apartment Type for chart
        $tenantsByApartmentType = DB::table('tenants')
            ->join('apartments', 'tenants.apartment_id', '=', 'apartments.id')
            ->where('apartments.owner_id', $ownerId)
            ->select('apartments.type', DB::raw('count(*) as count'))
            ->groupBy('apartments.type')
            ->get();
        
        // If no data, provide empty collection
        if ($tenantsByApartmentType->isEmpty()) {
            $tenantsByApartmentType = collect();
        }
        
        // Lease expiration data (adjust based on your actual database schema)
        $leaseExpirations = collect([
            (object)['month' => 'Jan', 'count' => rand(1, 8)],
            (object)['month' => 'Feb', 'count' => rand(1, 8)],
            (object)['month' => 'Mar', 'count' => rand(1, 8)],
            (object)['month' => 'Apr', 'count' => rand(1, 8)],
            (object)['month' => 'May', 'count' => rand(1, 8)],
            (object)['month' => 'Jun', 'count' => rand(1, 8)],
            (object)['month' => 'Jul', 'count' => rand(1, 8)],
            (object)['month' => 'Aug', 'count' => rand(1, 8)],
            (object)['month' => 'Sep', 'count' => rand(1, 8)],
            (object)['month' => 'Oct', 'count' => rand(1, 8)],
            (object)['month' => 'Nov', 'count' => rand(1, 8)],
            (object)['month' => 'Dec', 'count' => rand(1, 8)],
        ]);
        
        // Use the correct view path - owner.reports.tenants
        return view('owner.reports.tenants', compact(
            'tenants', 
            'totalTenants', 
            'activeTenants', 
            'inactiveTenants',
            'tenantsByApartmentType',
            'leaseExpirations'
        ));
    }

    /**
     * Get notifications for the current owner (AJAX) - Using Laravel's notification system.
     */
    public function getNotifications()
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Get the owner model instance
        $owner = Owner::find($ownerId);
        
        if (!$owner) {
            return response()->json([
                'notifications' => [],
                'unread_count' => 0
            ]);
        }

        // Get notifications using Laravel's notification system
        $notifications = $owner->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($n) {
                $data = $n->data;
                $type = $data['type'] ?? 'default';
                $icon = $this->getNotificationIcon($type);
                
                return [
                    'id' => $n->id,
                    'title' => $data['title'] ?? 'Notification',
                    'message' => $data['message'] ?? '',
                    'type' => $type,
                    'icon' => $icon,
                    'is_read' => !is_null($n->read_at),
                    'time_ago' => $n->created_at->diffForHumans(),
                    'data' => $data,
                ];
            });

        $unreadCount = $owner->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Get notification icon based on type.
     */
    private function getNotificationIcon($type)
    {
        $icons = [
            'message' => ['class' => 'created', 'icon' => 'fa-envelope'],
            'tenant' => ['class' => 'updated', 'icon' => 'fa-user-plus'],
            'apartment' => ['class' => 'created', 'icon' => 'fa-building'],
            'business' => ['class' => 'created', 'icon' => 'fa-store'],
            'payment' => ['class' => 'updated', 'icon' => 'fa-money-bill'],
            'maintenance' => ['class' => 'deleted', 'icon' => 'fa-tools'],
            'complaint' => ['class' => 'deleted', 'icon' => 'fa-exclamation-triangle'],
            'verification' => ['class' => 'created', 'icon' => 'fa-check-circle'],
            'welcome' => ['class' => 'created', 'icon' => 'fa-hand-peace'],
            'default' => ['class' => 'default', 'icon' => 'fa-bell']
        ];
        
        return $icons[$type] ?? $icons['default'];
    }

    /**
     * Mark a single notification as read (AJAX).
     */
    public function markNotificationRead($id)
    {
        $ownerId = Auth::guard('owner')->id();
        $owner = Owner::find($ownerId);
        
        if ($owner) {
            $notification = $owner->notifications()->where('id', $id)->first();
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true]);
            }
        }

        return response()->json(['success' => false], 404);
    }

    /**
     * Mark all notifications as read (AJAX).
     */
    public function markAllNotificationsRead()
    {
        $ownerId = Auth::guard('owner')->id();
        $owner = Owner::find($ownerId);
        
        if ($owner) {
            $owner->unreadNotifications->markAsRead();
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }

    /**
     * Get unread notification count (AJAX).
     */
    public function getUnreadNotificationCount()
    {
        $ownerId = Auth::guard('owner')->id();
        $owner = Owner::find($ownerId);
        
        $count = $owner ? $owner->unreadNotifications()->count() : 0;
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get dashboard statistics as JSON.
     */
    public function getStats()
    {
        $ownerId = Auth::guard('owner')->id();
        
        $totalApartments = Apartment::where('owner_id', $ownerId)->count();
        $occupiedUnits = Apartment::where('owner_id', $ownerId)
            ->where('status', 'Occupied')
            ->count();
        $vacantUnits = Apartment::where('owner_id', $ownerId)
            ->where('status', 'Vacant')
            ->count();
        
        return response()->json([
            'total_listings' => $totalApartments,
            'approved' => $occupiedUnits,
            'pending' => $vacantUnits,
            'total_tenants' => Tenant::whereHas('apartment', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })->count(),
            'occupancy_rate' => $totalApartments > 0 ? round(($occupiedUnits / $totalApartments) * 100, 2) : 0
        ]);
    }

    /**
     * Get annual tenant data for graph.
     */
    public function getAnnualData()
    {
        $ownerId = Auth::guard('owner')->id();
        $monthlyData = [];
        
        for ($month = 1; $month <= 12; $month++) {
            $count = Tenant::whereHas('apartment', function($q) use ($ownerId) {
                $q->where('owner_id', $ownerId);
            })
                ->whereMonth('created_at', $month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();
            
            $monthlyData[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'count' => $count
            ];
        }
        
        return response()->json($monthlyData);
    }

    /**
     * Get recent tenants as JSON.
     */
    public function getRecentTenants()
    {
        $ownerId = Auth::guard('owner')->id();
        
        $tenants = Tenant::whereHas('apartment', function($q) use ($ownerId) {
            $q->where('owner_id', $ownerId);
        })
            ->with('apartment')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($t) {
                return [
                    'id' => $t->id,
                    'name' => ($t->first_name ?? '') . ' ' . ($t->last_name ?? ''),
                    'email' => $t->email ?? '',
                    'phone' => $t->phone ?? 'N/A',
                    'apartment' => $t->apartment->unit_number ?? 'Not Assigned'
                ];
            });
        
        return response()->json($tenants);
    }

    /**
     * Get recent activities as JSON.
     */
    public function getRecentActivity()
    {
        // Get recent notifications as activities
        $ownerId = Auth::guard('owner')->id();
        $owner = Owner::find($ownerId);
        
        if (!$owner) {
            return response()->json([]);
        }
        
        $activities = $owner->notifications()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($n) {
                $data = $n->data;
                return [
                    'id' => $n->id,
                    'description' => ($data['title'] ?? 'Notification') . ': ' . ($data['message'] ?? ''),
                    'action' => $data['type'] ?? 'default',
                    'time_ago' => $n->created_at->diffForHumans(),
                    'created_at' => $n->created_at->toISOString()
                ];
            });
        
        return response()->json($activities);
    }

    /**
     * Get report statistics as JSON.
     */
    public function getReportStats()
    {
        // You can implement this to fetch from your reports table
        return response()->json(['total' => 0, 'recent' => 0]);
    }

    // ========== PROFILE & SETTINGS METHODS ==========

    /**
     * Update owner profile.
     */
    public function updateProfile(Request $request)
    {
        try {
            $owner = Auth::guard('owner')->user();
            
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:owners,email,' . $owner->id,
                'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            
            $owner->name = $request->name;
            $owner->email = $request->email;
            
            if ($request->hasFile('profile_photo')) {
                $photo = $request->file('profile_photo');
                $filename = time() . '_' . $photo->getClientOriginalName();
                $path = $photo->storeAs('profile_photos', $filename, 'public');
                $owner->profile_photo_url = 'storage/' . $path;
            }
            
            $owner->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update owner password.
     */
    public function updatePassword(Request $request)
    {
        try {
            $owner = Auth::guard('owner')->user();
            
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);
            
            // Check current password
            if (!Hash::check($request->current_password, $owner->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 400);
            }
            
            // Update password
            $owner->password = Hash::make($request->new_password);
            $owner->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Password updated successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}