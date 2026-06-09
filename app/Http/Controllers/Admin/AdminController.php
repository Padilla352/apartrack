<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }

        view()->share('admin', Auth::user());

        $totalApartments = $this->safeCount('apartments');
        $totalBusinesses = $this->safeCount('business_spaces');
        $pendingApprovals = $this->safeCountWhere('apartments', 'verification_status', 'pending');
        $totalUsers = $this->safeCount('users');

        return view('admin.dashboard.index', compact(
            'totalApartments',
            'totalBusinesses',
            'pendingApprovals',
            'totalUsers'
        ));
    }

    public function permitVerification()
    {
        if (!session()->has('admin_email')) return redirect()->route('login');
        
        view()->share('admin', Auth::user());

        // Get only PENDING applications
        $applicants = DB::table('permit_applications')
            ->leftJoin('owners', 'permit_applications.user_id', '=', 'owners.id')
            ->select(
                'permit_applications.*',
                'owners.name as owner_name',
                'owners.email as owner_email',
                'owners.phone as owner_phone'
            )
            ->where('permit_applications.status', 'pending')
            ->orderBy('permit_applications.created_at', 'desc')
            ->get()
            ->map(function($applicant) {
                // Check if permit number exists in permit_numbers table (ANY status)
                $permitExists = DB::table('permit_numbers')
                    ->where('permit_number', $applicant->permit_number)
                    ->exists();
                
                // Set can_approve flag (TRUE if permit exists in system, regardless of status)
                $applicant->can_approve = $permitExists ? 1 : 0;
                $applicant->applicant_name = $applicant->applicant_name ?? $applicant->owner_name ?? $applicant->name ?? 'N/A';
                $applicant->email = $applicant->email ?? $applicant->owner_email ?? 'No email';
                $applicant->property_name = $applicant->property_name ?? 'N/A';
                
                return $applicant;
            });

        // Count stats for the view
        $pendingCount = DB::table('permit_applications')->where('status', 'pending')->count();
        $approvedToday = DB::table('permit_applications')
            ->where('status', 'approved')
            ->whereDate('verified_at', today())
            ->count();
        $rejectedCount = DB::table('permit_applications')->where('status', 'rejected')->count();

        return view('admin.permit_verification.index', compact('applicants', 'pendingCount', 'approvedToday', 'rejectedCount'));
    }

    /**
     * Approve owner permit application
     * This will ADD the permit number to permit_numbers table if it doesn't exist
     */
    public function approveOwner($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $application = DB::table('permit_applications')->where('id', $id)->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found.']);
        }

        // Check if already approved
        if ($application->status === 'approved') {
            return response()->json(['success' => false, 'message' => 'Application already approved.']);
        }

        DB::beginTransaction();
        
        try {
            // UPDATE 1: Update permit_applications status to APPROVED
            DB::table('permit_applications')
                ->where('id', $id)
                ->update([
                    'status' => 'approved',
                    'verified_at' => now(),
                    'verified_by' => session('admin_id'),
                    'updated_at' => now(),
                ]);

            // UPDATE 2: Add or update permit number in permit_numbers table
            $existingPermit = DB::table('permit_numbers')
                ->where('permit_number', $application->permit_number)
                ->first();

            if (!$existingPermit) {
                // ADD NEW PERMIT NUMBER TO THE SYSTEM
                $insertData = [
                    'permit_number' => $application->permit_number,
                    'owner_name' => $application->applicant_name ?? $application->owner_name ?? 'Unknown',
                    'property_name' => $application->property_name ?? 'N/A',
                    'status' => 'used',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Add permit_type if column exists
                if (Schema::hasColumn('permit_numbers', 'permit_type')) {
                    $insertData['permit_type'] = $application->permit_type ?? 'residential';
                }
                
                // Add used_by if column exists
                if (Schema::hasColumn('permit_numbers', 'used_by') && $application->user_id) {
                    $insertData['used_by'] = $application->user_id;
                }
                
                // Add used_at if column exists
                if (Schema::hasColumn('permit_numbers', 'used_at')) {
                    $insertData['used_at'] = now();
                }
                
                DB::table('permit_numbers')->insert($insertData);
            } else {
                // UPDATE EXISTING PERMIT TO 'used' STATUS
                $updateData = ['status' => 'used', 'updated_at' => now()];
                
                if (Schema::hasColumn('permit_numbers', 'used_at')) {
                    $updateData['used_at'] = now();
                }
                if (Schema::hasColumn('permit_numbers', 'used_by') && $application->user_id) {
                    $updateData['used_by'] = $application->user_id;
                }
                
                DB::table('permit_numbers')
                    ->where('permit_number', $application->permit_number)
                    ->update($updateData);
            }

            // UPDATE 3: Update owners table
            if ($application->user_id) {
                // Update is_approved column if it exists
                if (Schema::hasColumn('owners', 'is_approved')) {
                    DB::table('owners')
                        ->where('id', $application->user_id)
                        ->update([
                            'is_approved' => 1,
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('owners')
                        ->where('id', $application->user_id)
                        ->update([
                            'updated_at' => now(),
                        ]);
                }
                
                // Update apartments if they exist
                if (Schema::hasTable('apartments') && Schema::hasColumn('apartments', 'owner_id')) {
                    DB::table('apartments')
                        ->where('owner_id', $application->user_id)
                        ->update([
                            'verification_status' => 'approved',
                            'verified_at' => now(),
                            'updated_at' => now(),
                        ]);
                }
            }

            DB::commit();

            // Create notification for successful approval
            $this->createNotification(
                'owner_approved',
                'Owner Approved',
                'Owner ' . ($application->applicant_name ?? $application->owner_name ?? 'Unknown') . ' has been approved.',
                'admin'
            );

            // Get updated pending count
            $newPendingCount = DB::table('permit_applications')->where('status', 'pending')->count();

            return response()->json([
                'success' => true, 
                'message' => 'Owner approved successfully! Permit number has been verified.',
                'pending_count' => $newPendingCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Approve Owner Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject owner permit application
     */
    public function rejectOwner(Request $request, $id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $application = DB::table('permit_applications')->where('id', $id)->first();

        if (!$application) {
            return response()->json(['success' => false, 'message' => 'Application not found.']);
        }

        // Check if already rejected
        if ($application->status === 'rejected') {
            return response()->json(['success' => false, 'message' => 'Application already rejected.']);
        }

        DB::beginTransaction();
        
        try {
            DB::table('permit_applications')
                ->where('id', $id)
                ->update([
                    'status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'updated_at' => now(),
                ]);

            // Update apartments if they exist
            if ($application->user_id && Schema::hasTable('apartments') && Schema::hasColumn('apartments', 'owner_id')) {
                DB::table('apartments')
                    ->where('owner_id', $application->user_id)
                    ->update([
                        'verification_status' => 'rejected',
                        'rejection_reason' => 'Owner permit verification failed: ' . $request->rejection_reason,
                        'updated_at' => now(),
                    ]);
            }

            DB::commit();

            // Create notification for rejection
            $this->createNotification(
                'owner_rejected',
                'Owner Rejected',
                'Owner ' . ($application->applicant_name ?? $application->owner_name ?? 'Unknown') . ' has been rejected. Reason: ' . $request->rejection_reason,
                'admin'
            );

            $newPendingCount = DB::table('permit_applications')->where('status', 'pending')->count();

            return response()->json([
                'success' => true, 
                'message' => 'Owner application rejected.',
                'pending_count' => $newPendingCount
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Reject Owner Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get notifications for admin (AJAX)
     */
    public function getNotifications()
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $notifications = Notification::where('target_role', 'admin')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
                ->map(function($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'title' => $notification->title,
                        'message' => $notification->message,
                        'is_read' => (bool)$notification->is_read,
                        'time_ago' => Carbon::parse($notification->created_at)->diffForHumans(),
                        'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                        'data' => $notification->data
                    ];
                });

            $unreadCount = Notification::where('target_role', 'admin')
                ->where('is_read', false)
                ->count();

            return response()->json([
                'success' => true,
                'notifications' => $notifications,
                'unread_count' => $unreadCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Get notifications error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'notifications' => [],
                'unread_count' => 0
            ]);
        }
    }

    /**
     * Mark a single notification as read
     */
    public function markNotificationRead($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $notification = Notification::where('id', $id)
                ->where('target_role', 'admin')
                ->first();

            if ($notification) {
                $notification->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsRead()
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            Notification::where('target_role', 'admin')
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper method to create notifications
     */
    private function createNotification($type, $title, $message, $targetRole = 'admin', $targetId = null, $data = [])
    {
        try {
            Notification::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'data' => json_encode($data),
                'target_role' => $targetRole,
                'target_id' => $targetId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to create notification: ' . $e->getMessage());
        }
    }

    public function reportsAnalytics()
    {
        if (!session()->has('admin_email')) return redirect()->route('login');
        
        view()->share('admin', Auth::user());

        $totalUnits = $this->safeCount('apartments');
        $occupiedUnits = $this->safeCountWhere('apartments', 'status', 'Occupied');
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;
        
        $stats = [
            'occupancyRate' => $occupancyRate,
            'avgVerification' => '0h',
            'newRegistrations' => $this->safeCountWhereMonth('users', 'created_at'),
            'rejectedPermits' => $this->safeCountWhere('permit_applications', 'status', 'rejected')
        ];
        
        $growthData = $this->getGrowthData();
        $distributionData = $this->getDistributionData();

        return view('admin.reports_analytics.index', compact('stats', 'growthData', 'distributionData'));
    }

    public function complaintsReports()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }
        
        view()->share('admin', Auth::user());

        $complaints = $this->safeGet('complaints', [], ['created_at', 'desc']);

        return view('admin.complaints_reports.index', compact('complaints'));
    }

    public function getAnalyticsData(Request $request)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $period = $request->get('period', 6);
        
        $totalUnits = $this->safeCount('apartments');
        $occupiedUnits = $this->safeCountWhere('apartments', 'status', 'Occupied');
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100) : 0;
        
        $pendingVerifications = $this->safeCountWhere('apartments', 'verification_status', 'pending');
        
        $currentMonthRegistrations = $this->safeCountWhereMonth('users', 'created_at');
        
        $lastMonthRegistrations = DB::table('users')
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $totalPermits = $this->safeCount('permit_applications');
        $rejectedPermits = $this->safeCountWhere('permit_applications', 'status', 'rejected');
        $rejectionRate = $totalPermits > 0 ? round(($rejectedPermits / $totalPermits) * 100) : 0;
        
        $avgVerification = 0;
        
        $growthData = $this->getGrowthDataForPeriod($period);
        
        $distributionData = [
            'apartments' => $this->safeCountWhere('apartments', 'type', 'apartment'),
            'boarding' => $this->safeCountWhere('apartments', 'type', 'boarding_house'),
            'commercial' => $this->safeCountWhere('apartments', 'type', 'commercial'),
        ];
        
        $recentActivity = $this->getRecentActivity();
        
        return response()->json([
            'success' => true,
            'stats' => [
                'occupancyRate' => $occupancyRate,
                'occupiedUnits' => $occupiedUnits,
                'totalUnits' => $totalUnits,
                'occupancyTrend' => $this->calculateTrend('occupancy'),
                'avgVerification' => round($avgVerification, 1),
                'pendingVerifications' => $pendingVerifications,
                'newRegistrations' => $currentMonthRegistrations,
                'lastMonthRegistrations' => $lastMonthRegistrations,
                'registrationsTrend' => $this->calculateTrend('registrations'),
                'rejectedPermits' => $rejectedPermits,
                'rejectionRate' => $rejectionRate,
                'totalPermits' => $totalPermits,
            ],
            'growthData' => $growthData,
            'distributionData' => $distributionData,
            'recentActivity' => $recentActivity,
            'lastUpdated' => now()->diffForHumans(),
        ]);
    }

    public function getDashboardData(Request $request)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $year = $request->get('year', date('Y'));
        
        $totalApartments = $this->safeCount('apartments');
        $totalBusinesses = $this->safeCount('business_spaces');
        $pendingApprovals = $this->safeCountWhere('apartments', 'verification_status', 'pending');
        $totalUsers = $this->safeCount('users');
        
        $apartmentTrend = $this->calculateMonthlyTrend('apartments');
        $businessTrend = $this->calculateMonthlyTrend('business_spaces');
        $userTrend = $this->calculateMonthlyTrend('users');
        
        $chartData = $this->getChartDataByYear($year);
        $activities = $this->getDashboardActivities();
        
        return response()->json([
            'success' => true,
            'stats' => [
                'totalApartments' => $totalApartments,
                'apartmentTrend' => $apartmentTrend,
                'totalBusinesses' => $totalBusinesses,
                'businessTrend' => $businessTrend,
                'pendingApprovals' => $pendingApprovals,
                'totalUsers' => $totalUsers,
                'userTrend' => $userTrend,
            ],
            'chartData' => $chartData,
            'activities' => $activities,
        ]);
    }

    /**
     * Verify admin password for sensitive actions (delete, deactivate, etc.)
     */
    public function verifyPassword(Request $request)
    {
        $request->validate(['password' => 'required']);

        $adminEmail = session('admin_email');
        if (!$adminEmail) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please login again.'], 401);
        }

        // Try to find admin in 'users' table with role = 'admin'
        $admin = \App\Models\User::where('email', $adminEmail)->where('role', 'admin')->first();

        // If not found in users table, try 'admins' table
        if (!$admin && Schema::hasTable('admins')) {
            $admin = DB::table('admins')->where('email', $adminEmail)->first();
            if ($admin && Hash::check($request->password, $admin->password)) {
                return response()->json(['success' => true]);
            }
        }

        // If found in users table
        if ($admin && Hash::check($request->password, $admin->password)) {
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid admin password.'], 401);
    }

    // ========== PRIVATE HELPER METHODS ==========

    private function safeCount($table)
    {
        try {
            if (Schema::hasTable($table)) {
                return DB::table($table)->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeCountWhere($table, $column, $value)
    {
        try {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                return DB::table($table)->where($column, $value)->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeCountWhereMonth($table, $column)
    {
        try {
            if (Schema::hasTable($table) && Schema::hasColumn($table, $column)) {
                return DB::table($table)
                    ->whereMonth($column, date('m'))
                    ->whereYear($column, date('Y'))
                    ->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeGet($table, $conditions = [], $orderBy = null, $limit = null)
    {
        try {
            if (!Schema::hasTable($table)) {
                return collect();
            }
            
            $query = DB::table($table);
            foreach ($conditions as $column => $value) {
                if (Schema::hasColumn($table, $column)) {
                    $query->where($column, $value);
                }
            }
            if ($orderBy) {
                $query->orderBy($orderBy[0], $orderBy[1] ?? 'desc');
            }
            if ($limit) {
                $query->limit($limit);
            }
            return $query->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    private function getGrowthDataForPeriod($months)
    {
        $months = (int)$months;
        $labels = [];
        $data = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M');
            $data[] = DB::table('users')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
        }
        
        return [
            $months => [
                'labels' => $labels,
                'data' => $data
            ]
        ];
    }

    private function getRecentActivity()
    {
        $activities = collect();
        
        $recentUsers = DB::table('users')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($user) {
                return (object)[
                    'type' => 'user',
                    'icon' => 'fa-user-plus',
                    'message' => "New user registered: {$user->name}",
                    'time_ago' => Carbon::parse($user->created_at)->diffForHumans()
                ];
            });
        
        $recentComplaints = collect();
        if (Schema::hasTable('complaints')) {
            $recentComplaints = DB::table('complaints')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function($complaint) {
                    return (object)[
                        'type' => 'complaint',
                        'icon' => 'fa-exclamation-triangle',
                        'message' => "New complaint: {$complaint->subject}",
                        'time_ago' => Carbon::parse($complaint->created_at)->diffForHumans()
                    ];
                });
        }
        
        $recentBusinesses = collect();
        if (Schema::hasTable('business_spaces')) {
            $recentBusinesses = DB::table('business_spaces')
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function($business) {
                    return (object)[
                        'type' => 'business',
                        'icon' => 'fa-store',
                        'message' => "New business registered: {$business->business_name}",
                        'time_ago' => Carbon::parse($business->created_at)->diffForHumans()
                    ];
                });
        }
        
        $recentPermitApplications = DB::table('permit_applications')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($application) {
                return (object)[
                    'type' => 'permit_application',
                    'icon' => 'fa-file-alt',
                    'message' => "New owner registration: {$application->applicant_name}",
                    'time_ago' => Carbon::parse($application->created_at)->diffForHumans()
                ];
            });
        
        $activities = $recentUsers
            ->concat($recentComplaints)
            ->concat($recentBusinesses)
            ->concat($recentPermitApplications)
            ->sortByDesc(function($item) {
                return $item->time_ago;
            })
            ->take(5)
            ->values();
        
        return $activities;
    }

    private function calculateTrend($type)
    {
        try {
            if ($type == 'occupancy') {
                $lastMonth = DB::table('apartments')
                    ->where('status', 'Occupied')
                    ->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()])
                    ->count();
                $currentMonth = DB::table('apartments')
                    ->where('status', 'Occupied')
                    ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                    ->count();
                
                if ($lastMonth > 0) {
                    return round((($currentMonth - $lastMonth) / $lastMonth) * 100);
                }
                return 0;
            }
            
            if ($type == 'registrations') {
                $lastMonth = DB::table('users')
                    ->whereMonth('created_at', now()->subMonth()->month)
                    ->count();
                $currentMonth = DB::table('users')
                    ->whereMonth('created_at', now()->month)
                    ->count();
                
                if ($lastMonth > 0) {
                    return round((($currentMonth - $lastMonth) / $lastMonth) * 100);
                }
                return 0;
            }
            
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateMonthlyTrend($table)
    {
        try {
            if (!Schema::hasTable($table)) {
                return 0;
            }
            
            $currentMonth = DB::table($table)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
            
            $lastMonth = DB::table($table)
                ->whereMonth('created_at', now()->subMonth()->month)
                ->whereYear('created_at', now()->subMonth()->year)
                ->count();
            
            if ($lastMonth > 0) {
                return round((($currentMonth - $lastMonth) / $lastMonth) * 100);
            }
            return $currentMonth > 0 ? 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getChartDataByYear($year)
    {
        $years = [2024, 2023, 2022];
        $result = [];
        
        foreach ($years as $y) {
            $users = [];
            $apartments = [];
            
            for ($month = 1; $month <= 12; $month++) {
                $users[] = DB::table('users')
                    ->whereYear('created_at', $y)
                    ->whereMonth('created_at', $month)
                    ->count();
                
                $apartments[] = DB::table('apartments')
                    ->whereYear('created_at', $y)
                    ->whereMonth('created_at', $month)
                    ->count();
            }
            
            $result[$y] = [
                'tenants' => $users,
                'apartments' => $apartments,
            ];
        }
        
        return $result;
    }

    private function getDashboardActivities()
    {
        $recentApprovals = DB::table('apartments')
            ->where('verification_status', 'approved')
            ->orderBy('verified_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return (object)[
                    'icon' => 'fa-check-circle',
                    'title' => $item->name ?? 'Apartment Listing',
                    'type' => 'Apartment',
                    'time_ago' => Carbon::parse($item->verified_at ?? $item->created_at)->diffForHumans()
                ];
            });
        
        if ($recentApprovals->isEmpty()) {
            $recentApprovals = DB::table('apartments')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
                ->map(function($item) {
                    return (object)[
                        'icon' => 'fa-building',
                        'title' => $item->name ?? 'New Apartment',
                        'type' => 'Apartment',
                        'time_ago' => Carbon::parse($item->created_at)->diffForHumans()
                    ];
                });
        }
        
        $pendingReviews = DB::table('apartments')
            ->where('verification_status', 'pending')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return (object)[
                    'icon' => 'fa-clock',
                    'title' => $item->name ?? 'Apartment Listing',
                    'type' => 'Pending Review',
                    'time_ago' => Carbon::parse($item->created_at)->diffForHumans()
                ];
            });
        
        return [
            'recentApprovals' => $recentApprovals,
            'pendingReviews' => $pendingReviews,
        ];
    }

    private function getGrowthData()
    {
        $months = [];
        $data = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('M');
            $data[] = DB::table('users')
                ->whereMonth('created_at', now()->subMonths($i)->month)
                ->whereYear('created_at', now()->subMonths($i)->year)
                ->count() ?? 0;
        }
        
        return ['labels' => $months, 'data' => $data];
    }

    private function getDistributionData()
    {
        return [
            'labels' => ['Apartments', 'Boarding House', 'Commercial'],
            'data' => [
                $this->safeCountWhere('apartments', 'type', 'apartment'),
                $this->safeCountWhere('apartments', 'type', 'boarding_house'),
                $this->safeCountWhere('apartments', 'type', 'commercial'),
            ]
        ];
    }
}