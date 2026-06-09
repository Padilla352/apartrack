<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Owner\Controller;
use App\Models\Apartment;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function occupancy()
    {
        // Temporarily remove owner filtering to test
        $totalApartments = Apartment::count();
        $occupiedUnits = Apartment::where('status', 'Occupied')->count();
        $vacantUnits = Apartment::where('status', 'Vacant')->count();
        $maintenanceUnits = Apartment::where('status', 'Maintenance')->count();
        $reservedUnits = Apartment::where('status', 'Reserved')->count();
        $occupancyRate = $totalApartments > 0 ? ($occupiedUnits / $totalApartments) * 100 : 0;
        
        $occupancyByType = Apartment::select('type')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "Occupied" THEN 1 ELSE 0 END) as occupied')
            ->selectRaw('SUM(CASE WHEN status = "Vacant" THEN 1 ELSE 0 END) as vacant')
            ->groupBy('type')
            ->get();
        
        $occupancyByBarangay = Apartment::with('barangay')
            ->select('barangay_id')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('SUM(CASE WHEN status = "Occupied" THEN 1 ELSE 0 END) as occupied')
            ->selectRaw('SUM(CASE WHEN status = "Vacant" THEN 1 ELSE 0 END) as vacant')
            ->groupBy('barangay_id')
            ->get();
        
        $monthlyTenantData = [];
        for ($month = 1; $month <= 12; $month++) {
            $count = Tenant::whereMonth('created_at', $month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();
            
            $monthlyTenantData[] = [
                'month' => Carbon::create()->month($month)->format('M'),
                'count' => $count
            ];
        }
        
        return view('owner.reports.occupancy', compact(
            'occupancyRate', 
            'occupancyByType', 
            'occupancyByBarangay',
            'totalApartments', 
            'occupiedUnits', 
            'vacantUnits',
            'maintenanceUnits', 
            'reservedUnits',
            'monthlyTenantData'
        ));
    }
    
    public function tenants()
    {
        // Temporarily remove owner filtering to test
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'Active')->count();
        $inactiveTenants = Tenant::where('status', 'Inactive')->count();
        $pendingTenants = Tenant::where('status', 'Pending')->count();
        
        $tenantsByApartmentType = Tenant::join('apartments', 'tenants.apartment_id', '=', 'apartments.id')
            ->select('apartments.type', \DB::raw('COUNT(*) as count'))
            ->groupBy('apartments.type')
            ->get();
        
        $leaseExpirations = Tenant::selectRaw('DATE_FORMAT(lease_end_date, "%Y-%m") as month')
            ->selectRaw('COUNT(*) as count')
            ->whereNotNull('lease_end_date')
            ->where('lease_end_date', '>=', Carbon::now())
            ->groupBy('month')
            ->orderBy('month')
            ->limit(12)
            ->get()
            ->map(function($item) {
                return [
                    'month' => Carbon::parse($item->month)->format('M Y'),
                    'count' => $item->count
                ];
            });
        
        $recentTenants = Tenant::with('apartment')
            ->latest()
            ->limit(10)
            ->get();
        
        return view('owner.reports.tenants', compact(
            'totalTenants', 
            'activeTenants', 
            'inactiveTenants', 
            'pendingTenants',
            'tenantsByApartmentType', 
            'leaseExpirations', 
            'recentTenants'
        ));
    }
    
    public function pending()
    {
        $pendingListings = Apartment::where('status', 'Vacant')
            ->with('barangay')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        $totalPending = Apartment::where('status', 'Vacant')->count();
        $recentPending = Apartment::where('status', 'Vacant')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        $pendingByType = Apartment::where('status', 'Vacant')
            ->select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->get();
        
        $pendingByBarangay = Apartment::where('status', 'Vacant')
            ->with('barangay')
            ->select('barangay_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('barangay_id')
            ->get();
        
        return view('owner.reports.pending', compact(
            'pendingListings',
            'totalPending',
            'recentPending',
            'pendingByType',
            'pendingByBarangay'
        ));
    }
    
    public function approved()
    {
        $approvedListings = Apartment::where('status', 'Occupied')
            ->with(['barangay', 'currentTenant'])
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        $totalApproved = Apartment::where('status', 'Occupied')->count();
        $recentApproved = Apartment::where('status', 'Occupied')
            ->where('updated_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        $approvedByType = Apartment::where('status', 'Occupied')
            ->select('type')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('type')
            ->get();
        
        $approvedByBarangay = Apartment::where('status', 'Occupied')
            ->with('barangay')
            ->select('barangay_id')
            ->selectRaw('COUNT(*) as count')
            ->groupBy('barangay_id')
            ->get();
        
        $monthlyApprovals = Apartment::where('status', 'Occupied')
            ->selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as month')
            ->selectRaw('COUNT(*) as count')
            ->where('updated_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();
        
        return view('owner.reports.approved', compact(
            'approvedListings',
            'totalApproved',
            'recentApproved',
            'approvedByType',
            'approvedByBarangay',
            'monthlyApprovals'
        ));
    }
    
    public function maintenance()
    {
        $maintenanceListings = Apartment::where('status', 'Maintenance')
            ->with('barangay')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        $totalMaintenance = Apartment::where('status', 'Maintenance')->count();
        
        return view('owner.reports.maintenance', compact(
            'maintenanceListings',
            'totalMaintenance'
        ));
    }
    
    public function reserved()
    {
        $reservedListings = Apartment::where('status', 'Reserved')
            ->with('barangay')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);
        
        $totalReserved = Apartment::where('status', 'Reserved')->count();
        
        return view('owner.reports.reserved', compact(
            'reservedListings',
            'totalReserved'
        ));
    }
    
    public function financial()
    {
        $monthlyIncome = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $monthName = $month->format('M Y');
            
            $income = Apartment::where('status', 'Occupied')
                ->sum('monthly_rent');
            
            $monthlyIncome[] = [
                'month' => $monthName,
                'income' => $income
            ];
        }
        
        $totalMonthlyIncome = Apartment::where('status', 'Occupied')
            ->sum('monthly_rent');
        $annualPotentialIncome = Apartment::sum('monthly_rent') * 12;
        $currentMonthlyIncome = Apartment::where('status', 'Occupied')
            ->sum('monthly_rent');
        
        return view('owner.reports.financial', compact(
            'monthlyIncome',
            'totalMonthlyIncome',
            'annualPotentialIncome',
            'currentMonthlyIncome'
        ));
    }
}