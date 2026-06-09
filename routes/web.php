<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\PageController;

// User (Tenant) Controllers
use App\Http\Controllers\Auth\LoginController as UserLoginController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\User\ProfileController;

// Owner Controllers
use App\Http\Controllers\Owner\Auth\LoginController as OwnerLoginController;
use App\Http\Controllers\Owner\Auth\RegisterController as OwnerRegisterController;
use App\Http\Controllers\Owner\ApartmentController as OwnerApartmentController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\BusinessSpaceController as OwnerBusinessSpaceController;
use App\Http\Controllers\Owner\MessageController as OwnerMessageController;

// Other Controllers
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\User\ApartmentController;
use App\Http\Controllers\User\ChatController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\User\NotificationController;

// Admin Controllers
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\ApartmentController as AdminApartmentController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\BusinessController;
use App\Http\Controllers\Admin\PermitNumberController;
use App\Http\Controllers\Admin\PermitListController;

// User Business Controller
use App\Http\Controllers\User\BusinessController as UserBusinessController;

/*
|--------------------------------------------------------------------------
| ROOT & PUBLIC REDIRECTS (NOW USING CONTROLLER)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::view('/about', 'user.about')->name('about');

/*
|--------------------------------------------------------------------------
| USER (TENANT) LOGIN - DEFAULT LOGIN PAGE
|--------------------------------------------------------------------------
*/
Route::get('/login', [UserLoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [UserLoginController::class, 'login'])->name('login.post');

/*
|--------------------------------------------------------------------------
| ADMIN AUTHENTICATION ROUTES - SEPARATE FROM USER LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin OTP & Password Reset Routes
Route::get('/admin/verify-otp', [AuthController::class, 'showOtpForm'])->name('admin.otp.view');
Route::post('/admin/verify-otp', [AuthController::class, 'verifyOtp'])->name('admin.otp.verify');
Route::get('/admin/forgot-password', [AuthController::class, 'showForgotPassword'])->name('admin.password.request');
Route::post('/admin/forgot-password', [AuthController::class, 'sendResetLink'])->name('admin.password.email');
Route::get('/admin/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('admin.password.reset');
Route::post('/admin/reset-password', [AuthController::class, 'updatePassword'])->name('admin.password.update');

/*
|--------------------------------------------------------------------------
| GUEST ROUTES (Not logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Owner auth
    Route::prefix('owner')->name('owner.')->group(function () {
        Route::get('/login', [OwnerLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [OwnerLoginController::class, 'login'])->name('login.submit');
        Route::get('/register', [OwnerRegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [OwnerRegisterController::class, 'register'])->name('register.submit');
        Route::get('/verify-otp', [OwnerRegisterController::class, 'showVerificationForm'])->name('verify.show');
        Route::post('/verify-otp', [OwnerRegisterController::class, 'verifyOtp'])->name('verify.otp');
        Route::post('/resend-otp', [OwnerRegisterController::class, 'resendOTP'])->name('verify.resend');
    });

    // Tenant registration
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.post');
    Route::get('/user/verify-otp', [RegisteredUserController::class, 'showOtpForm'])->name('otp.verify.view');
    Route::post('/user/verify-otp', [RegisteredUserController::class, 'verifyOtp'])->name('otp.verify.submit');
    Route::post('/user/verify-otp/resend', [RegisteredUserController::class, 'resendOtp'])->name('otp.verify.resend');

    // Public pages (Only show APPROVED apartments) – now using controller
    Route::get('/explore', [PageController::class, 'explore'])->name('explore');

    // Simple view routes (no closures)
    Route::view('/explore/boarding-nearby', 'explore.boarding-nearby')->name('explore.boarding.nearby');
    Route::view('/explore/commercial-nearby', 'explore.commercial-nearby')->name('explore.commercial.nearby');
    Route::view('/barangay-list', 'barangay')->name('guest.barangay');
    Route::get('/help', [HelpController::class, 'index'])->name('help');
});

/*
|--------------------------------------------------------------------------
| ADMIN PROTECTED ROUTES (Admin Session Required)
|--------------------------------------------------------------------------
*/
Route::middleware(['web'])->group(function () {
    Route::group(['middleware' => [
        function ($request, $next) {
            if (!session()->has('admin_email')) {
                return redirect()->route('admin.login');
            }
            return $next($request);
        }
    ]], function () {
        // Admin Dashboard
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/dashboard-data', [AdminController::class, 'getDashboardData'])->name('admin.dashboard.data');
        Route::post('/admin/verify-password', [AdminController::class, 'verifyPassword'])->name('admin.verify-password');
        Route::get('/admin/profile', function () {
            return redirect()->route('admin.dashboard');
        })->name('admin.profile');

        // Admin Notifications
        Route::get('/admin/notifications', [AdminController::class, 'getNotifications'])->name('admin.notifications');
        Route::post('/admin/notifications/{id}/read', [AdminController::class, 'markNotificationRead'])->name('admin.notification.read');
        Route::post('/admin/notifications/mark-all-read', [AdminController::class, 'markAllNotificationsRead'])->name('admin.notifications.mark-all-read');

        // Users Management
        Route::prefix('users-management')->name('users-management.')->group(function () {
            Route::get('/', [TenantController::class, 'index'])->name('index');
            Route::get('/tenants/list', [TenantController::class, 'tenantsList'])->name('tenants.list');
            Route::get('/tenants/view/{id}', [TenantController::class, 'viewTenant'])->name('tenants.view');
            Route::get('/tenants/edit/{id}', [TenantController::class, 'editTenant'])->name('tenants.edit');
            Route::get('/tenants/deactivate/{id}', [TenantController::class, 'deactivateTenant'])->name('tenants.deactivate');
            Route::get('/owners', [TenantController::class, 'ownersList'])->name('owners.list');
            Route::post('/tenants/update/{id}', [TenantController::class, 'updateTenant'])->name('tenants.update');
            Route::get('/tenants/create', [TenantController::class, 'create'])->name('tenants.create');
            Route::post('/tenants/store', [TenantController::class, 'store'])->name('tenants.store');
        });

        // Apartments Management (Admin)
        Route::prefix('admin/apartments')->name('admin.apartments.')->group(function () {
            Route::get('/', [AdminApartmentController::class, 'barangay'])->name('index');
            Route::get('/barangay', [AdminApartmentController::class, 'barangay'])->name('barangay');
            Route::get('/barangay/{slug}', [AdminApartmentController::class, 'showBarangay'])->name('barangay.show');
            Route::get('/details/{id}', [AdminApartmentController::class, 'viewApartmentDetails'])->name('details');
            Route::get('/create', [AdminApartmentController::class, 'create'])->name('create');
            Route::post('/store', [AdminApartmentController::class, 'store'])->name('store');
            Route::get('/edit/{id}', [AdminApartmentController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [AdminApartmentController::class, 'update'])->name('update');
            Route::delete('/destroy/{id}', [AdminApartmentController::class, 'destroy'])->name('destroy');
            Route::get('/load-more', [AdminApartmentController::class, 'loadMoreApartments'])->name('load-more');
            Route::get('/search', [AdminApartmentController::class, 'search'])->name('search');
            Route::get('/filter-by-price', [AdminApartmentController::class, 'filterByPrice'])->name('filter-by-price');
            Route::get('/pending', [AdminApartmentController::class, 'pendingVerifications'])->name('pending');
            Route::post('/approve/{id}', [AdminApartmentController::class, 'approveVerification'])->name('approve');
            Route::post('/reject/{id}', [AdminApartmentController::class, 'rejectVerification'])->name('reject');
            Route::get('/approved', [AdminApartmentController::class, 'approvedListings'])->name('approved');
            Route::get('/rejected', [AdminApartmentController::class, 'rejectedListings'])->name('rejected');
        });

        // Business Management (Admin)
        Route::prefix('admin/business')->name('admin.business.')->group(function () {
            Route::get('/', [BusinessController::class, 'index'])->name('index');
            Route::get('/details/{id}', [BusinessController::class, 'show'])->name('show');
            Route::get('/edit/{id}', [BusinessController::class, 'edit'])->name('edit');
            Route::put('/update/{id}', [BusinessController::class, 'update'])->name('update');
            Route::delete('/delete/{id}', [BusinessController::class, 'destroy'])->name('destroy');
            Route::get('/create', [BusinessController::class, 'create'])->name('create');
            Route::post('/store', [BusinessController::class, 'store'])->name('store');
            Route::get('/search', [BusinessController::class, 'search'])->name('search');
            Route::get('/pending', [BusinessController::class, 'pendingVerifications'])->name('pending');
            Route::post('/approve/{id}', [BusinessController::class, 'approveVerification'])->name('approve');
            Route::post('/reject/{id}', [BusinessController::class, 'rejectVerification'])->name('reject');
            Route::get('/barangay/{slug}', [BusinessController::class, 'showBarangay'])->name('barangay.show');
            Route::get('/approved', [BusinessController::class, 'approvedListings'])->name('approved');
            Route::get('/rejected', [BusinessController::class, 'rejectedListings'])->name('rejected');
            Route::post('/owner-approve/{id}', [BusinessController::class, 'approveOwner'])->name('owner.approve');
        });

        // Permit Numbers Management
        Route::prefix('permit-numbers')->name('permit-numbers.')->group(function () {
            Route::get('/', [PermitNumberController::class, 'index'])->name('index');
            Route::post('/store', [PermitNumberController::class, 'store'])->name('store');
            Route::delete('/{id}', [PermitNumberController::class, 'destroy'])->name('destroy');
        });

        // Permit Verification (Owner Verification)
        Route::prefix('permit-verification')->name('admin.permit-verification.')->group(function () {
            Route::get('/', [AdminController::class, 'permitVerification'])->name('index');
            Route::post('/{id}/approve', [AdminController::class, 'approveOwner'])->name('approve');
            Route::post('/{id}/reject', [AdminController::class, 'rejectOwner'])->name('reject');
        });
        Route::get('/permit-verification-list', [AdminController::class, 'permitVerification'])->name('permits.index');

        // Reports & Analytics
        Route::get('/reports-analytics', [AdminController::class, 'reportsAnalytics'])->name('reports.analytics');
        Route::get('/reports-analytics-data', [AdminController::class, 'getAnalyticsData'])->name('reports.analytics.data');

        // Complaints
        Route::prefix('complaints')->name('complaints.')->group(function () {
            Route::get('/', [ComplaintController::class, 'index'])->name('index');
            Route::get('/{id}', [ComplaintController::class, 'show'])->name('show');
            Route::post('/{id}/resolve', [ComplaintController::class, 'resolve'])->name('resolve');
            Route::post('/', [ComplaintController::class, 'store'])->name('store');
        });

        // Admin Settings
        Route::prefix('admin/settings')->name('admin.settings.')->group(function () {
            Route::get('/', [AdminSettingsController::class, 'index'])->name('index');
            Route::post('/update', [AdminSettingsController::class, 'updateProfile'])->name('update');
            Route::post('/password', [AdminSettingsController::class, 'updatePassword'])->name('password');
        });
    });
});

/*
|--------------------------------------------------------------------------
| OWNER PROTECTED ROUTES (FULLY UPDATED CHAT ROUTES)
|--------------------------------------------------------------------------
*/
Route::prefix('owner')->name('owner.')->middleware('auth:owner')->group(function () {
    // Dashboard
    Route::get('/dashboard', [OwnerDashboardController::class, 'index'])->name('dashboard');
    Route::get('/api/recent-activities', [OwnerDashboardController::class, 'getRecentActivity'])->name('api.recent-activities');
    Route::post('/logout', [OwnerLoginController::class, 'logout'])->name('logout');

    // Profile
    Route::post('/update-profile', [OwnerDashboardController::class, 'updateProfile'])->name('update.profile');
    Route::post('/update-password', [OwnerDashboardController::class, 'updatePassword'])->name('update.password');

    // Apartments
    Route::resource('apartments', OwnerApartmentController::class)->except(['create']);
    Route::get('/apartments/revise/{id}', [OwnerApartmentController::class, 'revise'])->name('apartments.revise');
    Route::put('/apartments/resubmit/{id}', [OwnerApartmentController::class, 'resubmit'])->name('apartments.resubmit');

    // Business Spaces
    Route::resource('business-spaces', OwnerBusinessSpaceController::class);
    Route::get('business-spaces/revise/{id}', [OwnerBusinessSpaceController::class, 'revise'])->name('business-spaces.revise');
    Route::put('business-spaces/resubmit/{id}', [OwnerBusinessSpaceController::class, 'resubmit'])->name('business-spaces.resubmit');
    Route::post('/business/store', [OwnerApartmentController::class, 'storeBusiness'])->name('business.store');

    // ==================== OWNER CHAT ROUTES (UPDATED WITH AJAX) ====================
    Route::prefix('messages')->name('messages.')->group(function () {
        // Page routes
        Route::get('/', [OwnerMessageController::class, 'index'])->name('index');
        Route::get('/create', [OwnerMessageController::class, 'create'])->name('create');
        Route::post('/', [OwnerMessageController::class, 'store'])->name('store');
        Route::get('/{conversation}', [OwnerMessageController::class, 'show'])->name('show');
        Route::post('/{conversation}/reply', [OwnerMessageController::class, 'reply'])->name('reply');
        Route::delete('/{conversation}', [OwnerMessageController::class, 'destroy'])->name('destroy');
        
        // AJAX routes for real-time chat
        Route::get('/{conversation}/fetch', [OwnerMessageController::class, 'fetchMessages'])->name('fetch');
        Route::post('/{conversation}/send', [OwnerMessageController::class, 'sendMessage'])->name('send');
        
        // Utility routes
        Route::get('/unread/count', [OwnerMessageController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/recent/list', [OwnerMessageController::class, 'getRecentMessages'])->name('recent');
    });
    // =====================================================================

    // ==================== NOTIFICATION ROUTES ====================
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'getNotifications'])->name('index');
        Route::get('/unread-count', [OwnerDashboardController::class, 'getUnreadNotificationCount'])->name('unread-count');
        Route::post('/{id}/read', [OwnerDashboardController::class, 'markNotificationRead'])->name('read');
        Route::post('/mark-all-read', [OwnerDashboardController::class, 'markAllNotificationsRead'])->name('mark-all-read');
    });
    // ========================================================================

    // Tenants Management
    Route::prefix('tenants')->name('tenants.')->group(function () {
        Route::get('/', [OwnerDashboardController::class, 'tenantsList'])->name('index');
        Route::get('/create', [OwnerDashboardController::class, 'createTenant'])->name('create');
        Route::post('/', [OwnerDashboardController::class, 'storeTenant'])->name('store');
        Route::get('/{id}', [OwnerDashboardController::class, 'showTenant'])->name('show');
        Route::get('/{id}/edit', [OwnerDashboardController::class, 'editTenant'])->name('edit');
        Route::put('/{id}', [OwnerDashboardController::class, 'updateTenant'])->name('update');
        Route::delete('/{id}', [OwnerDashboardController::class, 'destroyTenant'])->name('destroy');
    });

    // Reports & Profile
    Route::get('/reports/tenants', [OwnerDashboardController::class, 'reports'])->name('reports.tenants');
    Route::get('/profile', [OwnerDashboardController::class, 'profile'])->name('profile');
    Route::get('/settings', [OwnerDashboardController::class, 'settings'])->name('settings');
});

/*
|--------------------------------------------------------------------------
| TENANT AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/home', [DashboardController::class, 'index'])->name('home');
    Route::post('/logout', [UserLoginController::class, 'logout'])->name('logout');

    // ==================== TENANT CHAT ROUTES ====================
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [ChatController::class, 'indexForTenant'])->name('index');
        Route::get('/start/{ownerId}', [ChatController::class, 'start'])->name('start');
        Route::get('/{conversation}', [ChatController::class, 'show'])->name('show');
        Route::post('/send', [ChatController::class, 'sendMessage'])->name('send');
        Route::post('/{conversation}/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
        Route::post('/typing', [ChatController::class, 'typing'])->name('typing');
        Route::get('/unread/count', [ChatController::class, 'getUnreadCount'])->name('unread-count');
        Route::get('/conversations/list', [ChatController::class, 'getConversations'])->name('conversations');
        Route::get('/messages/{conversation}', [ChatController::class, 'fetchMessages'])->name('messages');
    });
    // =============================================================

    // API endpoints for realtime chat
    Route::prefix('api/chat')->group(function () {
        Route::post('/messages', [ChatController::class, 'sendMessage']);
        Route::get('/messages/{conversation}', [ChatController::class, 'fetchMessages']);
        Route::post('/mark-read/{conversation}', [ChatController::class, 'markAsRead']);
        Route::post('/typing', [ChatController::class, 'typing']);
    });

    // Profile & Settings
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::post('/profile/cover', [ProfileController::class, 'updateCover'])->name('profile.cover');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/settings', [ProfileController::class, 'settings'])->name('settings');
    Route::put('/settings/profile', [ProfileController::class, 'updateSettingsProfile'])->name('settings.profile');
    Route::post('/settings/avatar', [ProfileController::class, 'updateAvatar'])->name('settings.avatar');
    Route::put('/settings/password', [ProfileController::class, 'updateSettingsPassword'])->name('settings.password');
    Route::post('/settings/notifications', [ProfileController::class, 'updateNotifications'])->name('settings.notifications');

    Route::post('/logout/all', [ProfileController::class, 'logoutAllDevices'])->name('logout.all');

    // Business contact form
    Route::post('/business/{id}/contact', [UserBusinessController::class, 'sendContact'])->name('business.contact');
});

/*
|--------------------------------------------------------------------------
| REAL‑TIME NOTIFICATION ROUTES (FOR TENANTS/USERS)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

/*
|--------------------------------------------------------------------------
| GENERIC REDIRECT ROUTES (NOW USING CONTROLLER)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard-redirect', [PageController::class, 'dashboardRedirect'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| PUBLIC APARTMENT & BARANGAY DETAILS (INCLUDING BUSINESS SPACES) – CONTROLLER
|--------------------------------------------------------------------------
*/
Route::get('/barangay-details', [PageController::class, 'barangayDetails'])->name('barangay.details');

/*
|--------------------------------------------------------------------------
| APARTMENT DETAILS (LEGACY)
|--------------------------------------------------------------------------
*/
Route::get('/apartment/{barangayId}/{apartmentId}', [ApartmentController::class, 'show'])
    ->where(['barangayId' => '[a-zA-Z0-9-]+', 'apartmentId' => '[0-9]+'])
    ->name('apartment.details');

/*
|--------------------------------------------------------------------------
| SOCIAL LOGIN
|--------------------------------------------------------------------------
*/
Route::get('/auth/facebook/redirect', [SocialiteController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('/auth/facebook/callback', [SocialiteController::class, 'handleFacebookCallback'])->name('facebook.callback');
Route::get('/auth/google/redirect', [SocialiteController::class, 'redirectToGoogle'])->name('google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'handleGoogleCallback'])->name('google.callback');

/*
|--------------------------------------------------------------------------
| DEBUG & ADDITIONAL PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('admin/permit-verification/debug', [PermitListController::class, 'debugStatus'])->name('admin.permit-verification.debug');

Route::view('/boarding-nearby', 'explore.boarding-nearby')->name('boarding.nearby');
Route::view('/commercial-nearby', 'explore.commercial-nearby')->name('commercial.nearby');
Route::post('/help/feedback', [HelpController::class, 'storeFeedback'])->name('help.feedback');

// Remove or comment the test event route – it uses a closure and is not needed in production
// Route::get('/test-event', function () { ... }) – DELETE THIS LINE.

/*
|--------------------------------------------------------------------------
| PUBLIC BUSINESS SPACES ROUTES (USER SIDE)
|--------------------------------------------------------------------------
*/
Route::get('/businesses', [UserBusinessController::class, 'index'])->name('user.businesses.index');
Route::get('/businesses/{id}', [UserBusinessController::class, 'show'])->name('user.businesses.show');

// Modern apartment details route (used in barangay view) – renamed to avoid duplicate name
Route::get('/{barangaySlug}/apartment/{apartmentId}', [ApartmentController::class, 'show'])->name('barangay.apartment.details');