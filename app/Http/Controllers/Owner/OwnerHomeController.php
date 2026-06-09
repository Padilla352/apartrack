<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Owner\Controller;
use App\Models\Apartment;
use App\Models\Tenant;
use App\Models\Barangay;
use App\Models\ActivityLog;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Get all statistics for the dashboard
        $totalApartments = Apartment::count();
        $occupiedUnits = Apartment::where('status', 'Occupied')->count();
        $vacantUnits = Apartment::where('status', 'Vacant')->count();
        $maintenanceUnits = Apartment::where('status', 'Maintenance')->count();
        $reservedUnits = Apartment::where('status', 'Reserved')->count();
        $totalTenants = Tenant::where('status', 'Active')->count();
        $pendingTenants = Tenant::where('status', 'Pending')->count();
        
        // Get message statistics
        $unreadMessages = Message::where('receiver_id', $ownerId)
            ->where('status', 'unread')
            ->count();
        
        // Get report statistics
        $totalReports = ActivityLog::count();
        $recentReports = ActivityLog::where('created_at', '>=', now()->subDays(30))->count();
        
        $stats = [
            'total_apartments' => $totalApartments,
            'total_listings' => $totalApartments,
            'occupied_units' => $occupiedUnits,
            'approved' => $occupiedUnits,
            'vacant_units' => $vacantUnits,
            'pending' => $vacantUnits,
            'total_tenants' => $totalTenants,
            'pending_tenants' => $pendingTenants,
            'occupancy_rate' => $this->calculateOccupancyRate(),
            'maintenance' => $maintenanceUnits,
            'reserved' => $reservedUnits,
            'reports' => $totalReports,
            'recent_reports' => $recentReports,
            'unread_messages' => $unreadMessages
        ];
        
        // Get monthly tenant data for the graph (last 12 months)
        $monthlyTenantData = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthName = $month->format('M');
            
            // Count tenants who were active during this month
            $tenantCount = Tenant::where('status', 'Active')
                ->where('move_in_date', '<=', $month->endOfMonth())
                ->where(function($query) use ($month) {
                    $query->whereNull('lease_end_date')
                          ->orWhere('lease_end_date', '>=', $month->startOfMonth());
                })
                ->count();
            
            $monthlyTenantData[] = [
                'month' => $monthName,
                'count' => $tenantCount
            ];
        }
        
        // Get recent tenants
        $recentTenants = Tenant::with('apartment')
            ->latest()
            ->limit(5)
            ->get();
        
        // Get vacant units (pending listings)
        $vacantUnitsList = Apartment::where('status', 'Vacant')
            ->limit(5)
            ->get();
        
        // Get recent activities
        $recentActivities = ActivityLog::latest()->limit(5)->get();
        
        // Get expiring leases (next 30 days)
        $expiringLeases = Tenant::where('status', 'Active')
            ->whereNotNull('lease_end_date')
            ->whereDate('lease_end_date', '<=', now()->addDays(30))
            ->whereDate('lease_end_date', '>=', now())
            ->with('apartment')
            ->limit(5)
            ->get();
        
        // Get pending approvals (vacant apartments)
        $pendingApprovals = Apartment::where('status', 'Vacant')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get all barangays for the modal dropdown
        $barangays = Barangay::orderBy('name')->get();
        
        // Get recent listings (latest apartments)
        $recentListings = Apartment::with('barangay')
            ->latest()
            ->limit(5)
            ->get();
        
        return view('owner.dashboard', compact(
            'stats',
            'monthlyTenantData',
            'recentTenants',
            'vacantUnitsList',
            'recentActivities',
            'expiringLeases',
            'pendingApprovals',
            'barangays',
            'recentListings',
            'totalTenants'
        ));
    }
    
    /**
     * Calculate the overall occupancy rate.
     */
    private function calculateOccupancyRate(): float
    {
        $total = Apartment::count();
        $occupied = Apartment::where('status', 'Occupied')->count();
        
        return $total > 0 ? round(($occupied / $total) * 100, 2) : 0;
    }
    
    /**
     * Get dashboard statistics via API (for AJAX updates).
     */
    public function getStats()
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            $stats = [
                'total_listings' => Apartment::count(),
                'approved' => Apartment::where('status', 'Occupied')->count(),
                'pending' => Apartment::where('status', 'Vacant')->count(),
                'maintenance' => Apartment::where('status', 'Maintenance')->count(),
                'reserved' => Apartment::where('status', 'Reserved')->count(),
                'occupancy_rate' => $this->calculateOccupancyRate(),
                'total_tenants' => Tenant::where('status', 'Active')->count(),
                'reports' => ActivityLog::count(),
                'unread_messages' => Message::where('receiver_id', $ownerId)
                    ->where('status', 'unread')
                    ->count()
            ];
            
            return response()->json($stats);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get recent activity for notifications - UPDATED with 'action' field.
     */
    public function getRecentActivity()
    {
        try {
            $activities = ActivityLog::latest()->limit(10)->get()->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'action' => $activity->action ?? 'info', // Added action field
                    'time_ago' => $activity->created_at->diffForHumans(),
                    'created_at' => $activity->created_at->toIso8601String(),
                    'icon' => match($activity->action) {
                        'created' => 'plus-circle',
                        'updated' => 'edit',
                        'deleted' => 'trash-alt',
                        default => 'info-circle'
                    },
                    'color' => match($activity->action) {
                        'created' => '#B4E662',
                        'updated' => '#FFEB3B',
                        'deleted' => '#D90404',
                        default => '#007BFF'
                    }
                ];
            });
            
            // If no activities, return a welcome message
            if ($activities->isEmpty()) {
                return response()->json([
                    [
                        'id' => 0,
                        'description' => 'Welcome to APARTrack! Start by adding your first property.',
                        'action' => 'created',
                        'time_ago' => 'Just now',
                        'created_at' => now()->toIso8601String(),
                        'icon' => 'info-circle',
                        'color' => '#007BFF'
                    ]
                ]);
            }
            
            return response()->json($activities);
        } catch (\Exception $e) {
            return response()->json([
                [
                    'id' => 0,
                    'description' => 'Welcome to APARTrack!',
                    'action' => 'info',
                    'time_ago' => 'Just now',
                    'icon' => 'info-circle',
                    'color' => '#007BFF'
                ]
            ]);
        }
    }
    
    /**
     * Get recent listings for the dashboard.
     */
    public function getRecentListings()
    {
        try {
            $listings = Apartment::with('barangay')
                ->latest()
                ->limit(10)
                ->get()
                ->map(function($apartment) {
                    return [
                        'id' => $apartment->id,
                        'unit_number' => $apartment->unit_number,
                        'name' => $apartment->name,
                        'type' => $apartment->type,
                        'monthly_rent' => $apartment->monthly_rent,
                        'status' => $apartment->status,
                        'status_text' => match($apartment->status) {
                            'Occupied' => 'Approved',
                            'Vacant' => 'Pending',
                            'Maintenance' => 'Maintenance',
                            'Reserved' => 'Reserved',
                            default => 'Unknown'
                        },
                        'status_class' => match($apartment->status) {
                            'Occupied' => 'status-approved',
                            'Vacant' => 'status-pending',
                            'Maintenance' => 'status-warning',
                            'Reserved' => 'status-info',
                            default => 'status-secondary'
                        },
                        'image_url' => $apartment->image_url,
                        'barangay' => $apartment->barangay->name ?? 'N/A',
                        'created_at' => $apartment->created_at->diffForHumans()
                    ];
                });
            
            return response()->json($listings);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get monthly tenant data for the graph.
     */
    public function getMonthlyTenantData()
    {
        try {
            $monthlyData = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthName = $month->format('M');
                
                $tenantCount = Tenant::where('status', 'Active')
                    ->where('move_in_date', '<=', $month->endOfMonth())
                    ->where(function($query) use ($month) {
                        $query->whereNull('lease_end_date')
                              ->orWhere('lease_end_date', '>=', $month->startOfMonth());
                    })
                    ->count();
                
                $monthlyData[] = [
                    'month' => $monthName,
                    'count' => $tenantCount
                ];
            }
            
            return response()->json($monthlyData);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    /**
     * Get expiring leases for notifications.
     */
    public function getExpiringLeases()
    {
        try {
            $leases = Tenant::where('status', 'Active')
                ->whereNotNull('lease_end_date')
                ->whereDate('lease_end_date', '<=', now()->addDays(30))
                ->whereDate('lease_end_date', '>=', now())
                ->with('apartment')
                ->get()
                ->map(function($tenant) {
                    $daysLeft = now()->diffInDays($tenant->lease_end_date, false);
                    return [
                        'id' => $tenant->id,
                        'tenant_name' => $tenant->full_name,
                        'apartment_name' => $tenant->apartment->name ?? 'N/A',
                        'lease_end_date' => $tenant->lease_end_date->format('Y-m-d'),
                        'days_left' => max(0, $daysLeft),
                        'priority' => $daysLeft <= 7 ? 'high' : ($daysLeft <= 14 ? 'medium' : 'low')
                    ];
                });
            
            return response()->json($leases);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}