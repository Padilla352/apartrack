<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller\Owner;
use App\Models\Apartment;
use App\Models\Tenant;
use App\Models\ActivityLog;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Get real apartment statistics
        $totalApartments = Apartment::count();
        $occupiedUnits = Apartment::where('status', 'Occupied')->count();
        $vacantUnits = Apartment::where('status', 'Vacant')->count();
        $maintenanceUnits = Apartment::where('status', 'Maintenance')->count();
        $reservedUnits = Apartment::where('status', 'Reserved')->count();
        
        // Get real tenant statistics
        $totalTenants = Tenant::where('status', 'Active')->count();
        $pendingTenants = Tenant::where('status', 'Pending')->count();
        $inactiveTenants = Tenant::where('status', 'Inactive')->count();
        
        // Get message statistics
        $unreadMessages = Message::where('receiver_id', $ownerId)
            ->where('status', 'unread')
            ->count();
        
        $totalMessages = Message::where(function($q) use ($ownerId) {
                $q->where('receiver_id', $ownerId)
                  ->orWhere('sender_id', $ownerId);
            })->count();
        
        // Get report statistics (activity logs)
        $totalReports = ActivityLog::count();
        $recentReports = ActivityLog::where('created_at', '>=', now()->subDays(30))->count();
        
        // Prepare stats array for dashboard
        $stats = [
            'total_listings' => $totalApartments,
            'approved' => $occupiedUnits,
            'pending' => $vacantUnits,
            'maintenance' => $maintenanceUnits,
            'reserved' => $reservedUnits,
            'total_tenants' => $totalTenants,
            'pending_tenants' => $pendingTenants,
            'reports' => $totalReports,
            'recent_reports' => $recentReports,
            'unread_messages' => $unreadMessages,
            'total_messages' => $totalMessages,
            'occupancy_rate' => $totalApartments > 0 ? round(($occupiedUnits / $totalApartments) * 100, 2) : 0
        ];
        
        // Get monthly tenant data for graph (last 12 months)
        $monthlyTenantData = [];
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
            
            $monthlyTenantData[] = [
                'month' => $monthName,
                'count' => $tenantCount
            ];
        }
        
        // Get recent activities with real data
        $recentActivities = ActivityLog::latest()->limit(10)->get();
        
        // Get expiring leases (next 30 days)
        $expiringLeases = Tenant::where('status', 'Active')
            ->whereNotNull('lease_end_date')
            ->whereDate('lease_end_date', '<=', now()->addDays(30))
            ->whereDate('lease_end_date', '>=', now())
            ->with('apartment')
            ->limit(5)
            ->get();
        
        // Get recent tenants for the email list
        $recentTenants = Tenant::with('apartment')
            ->latest()
            ->limit(5)
            ->get();
        
        // Get pending approvals (vacant apartments that need attention)
        $pendingApprovals = Apartment::where('status', 'Vacant')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('owner.dashboard', compact(
            'stats',
            'totalTenants',
            'monthlyTenantData',
            'recentActivities',
            'expiringLeases',
            'recentTenants',
            'pendingApprovals'
        ));
    }
    
    // API method for dashboard stats
    public function getStats()
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            return response()->json([
                'total_listings' => Apartment::count(),
                'approved' => Apartment::where('status', 'Occupied')->count(),
                'pending' => Apartment::where('status', 'Vacant')->count(),
                'maintenance' => Apartment::where('status', 'Maintenance')->count(),
                'reserved' => Apartment::where('status', 'Reserved')->count(),
                'total_tenants' => Tenant::where('status', 'Active')->count(),
                'reports' => ActivityLog::count(),
                'unread_messages' => Message::where('receiver_id', $ownerId)
                    ->where('status', 'unread')
                    ->count(),
                'occupancy_rate' => Apartment::getOverallOccupancyRate()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'total_listings' => 0,
                'approved' => 0,
                'pending' => 0,
                'maintenance' => 0,
                'reserved' => 0,
                'total_tenants' => 0,
                'reports' => 0,
                'unread_messages' => 0,
                'occupancy_rate' => 0
            ]);
        }
    }
    
    // API method for recent activity
    public function getRecentActivity()
    {
        try {
            $activities = ActivityLog::latest()->limit(10)->get()->map(function($activity) {
                return [
                    'id' => $activity->id,
                    'description' => $activity->description,
                    'time_ago' => $activity->created_at->diffForHumans(),
                    'created_at' => $activity->created_at->toIso8601String()
                ];
            });
            return response()->json($activities);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
    
    // API method for recent listings
    public function getRecentListings()
    {
        try {
            $listings = Apartment::latest()->limit(5)->get()->map(function($apartment) {
                return [
                    'id' => $apartment->id,
                    'name' => $apartment->name,
                    'type' => $apartment->type,
                    'status' => $apartment->status == 'Occupied' ? 'Approved' : 'Pending',
                    'price' => number_format($apartment->monthly_rent, 2),
                    'created_at' => $apartment->created_at->diffForHumans()
                ];
            });
            return response()->json($listings);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }
}
