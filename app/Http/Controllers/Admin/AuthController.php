<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminOtpMail;
use App\Mail\AdminResetPasswordMail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Auth; // Added for Auth facade

class AuthController extends Controller
{
    // SHOW LOGIN FORM
    public function showLogin()
    {
        if (session()->has('admin_email')) {
            return redirect()->route('dashboard');
        }
        return view('Admin.login'); 
    }

    // HANDLE LOGIN REQUEST
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // RATE LIMITER CHECK
        $key = 'login-attempt:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error', "Masyadong maraming mali. Subukan muli pagkatapos ng $seconds segundo.");
        }

        $admin = DB::table('admins')->where('email', $request->email)->first();

        if (!$admin) {
            RateLimiter::hit($key, 600);
            return back()
                ->with('error', 'Maling email o password.')
                ->withInput($request->only('email'));
        }

        // PASSWORD VALIDATION
        $isValid = false;
        if (password_get_info($admin->password)['algo'] !== 0) {
            $isValid = Hash::check($request->password, $admin->password);
        } else {
            $isValid = ($request->password === $admin->password);
            if ($isValid) {
                DB::table('admins')
                    ->where('id', $admin->id)
                    ->update(['password' => Hash::make($request->password)]);
            }
        }

        if (!$isValid) {
            RateLimiter::hit($key, 600);
            return back()
                ->with('error', 'Maling email o password.')
                ->withInput($request->only('email'));
        }

        RateLimiter::clear($key);

        $otp = rand(100000, 999999);

        session([
            'temp_admin_id'    => $admin->id,
            'temp_admin_email' => $admin->email,
            'temp_admin_name'  => $admin->name ?? 'Admin',
            'login_otp'        => $otp,
            'otp_expires_at'   => now()->addMinutes(10),
        ]);

        try {
            Mail::to($admin->email)->send(new AdminOtpMail($otp));
        } catch (\Exception $e) {
            return back()->with('error', 'Mail Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.otp.view');
    }

    // SHOW OTP VERIFICATION FORM
    public function showOtpForm()
    {
        if (!session()->has('temp_admin_id')) {
            return redirect()->route('login');
        }
        return view('Admin.otp-verify'); 
    }

    // VERIFY OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);

        $storedOtp = session('login_otp');
        $expiresAt = session('otp_expires_at');

        if ($request->otp == $storedOtp && now()->isBefore($expiresAt)) {
            $request->session()->regenerate();
            $request->session()->put('admin_email', session('temp_admin_email'));
            $request->session()->put('admin_id', session('temp_admin_id'));
            $request->session()->put('admin_name', session('temp_admin_name'));

            session()->forget(['temp_admin_id', 'temp_admin_email', 'temp_admin_name', 'login_otp', 'otp_expires_at']);

            return redirect()->route('dashboard');
        }

        return back()->with('error', 'Invalid or expired OTP code.');
    }

    // SHOW FORGOT PASSWORD FORM
    public function showForgotPassword()
    {
        return view('Admin.emails.forgot-password');
    }

    // SEND PASSWORD RESET LINK
    public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $admin = DB::table('admins')->where('email', $request->email)->first();

    if (!$admin) {
        return back()->with('error', 'Email address not found.');
    }

    $token = Str::random(64);

    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $token, 'created_at' => Carbon::now()]
    );

    // FIX: Use the correct route name that matches your web.php
    $url = route('admin.password.reset', ['token' => $token, 'email' => $request->email]);

    try {
        // Pass the URL to the mail class
        Mail::to($admin->email)->send(new AdminResetPasswordMail($url));
    } catch (\Exception $e) {
        return back()->with('error', 'Mail Error: ' . $e->getMessage());
    }

    return back()->with('status', 'Password reset link sent! Please check your email.');
}

    // SHOW RESET PASSWORD FORM
    public function showResetPassword($token)
{
    $email = request()->query('email');
    
    // Verify the token exists and is valid
    $resetData = DB::table('password_reset_tokens')
        ->where('token', $token)
        ->where('email', $email)
        ->first();
    
    if (!$resetData) {
        return redirect()->route('admin.password.request')
            ->with('error', 'Invalid reset link. Please request a new one.');
    }
    
    if (Carbon::parse($resetData->created_at)->addMinutes(60)->isPast()) {
        DB::table('password_reset_tokens')->where('token', $token)->delete();
        return redirect()->route('admin.password.request')
            ->with('error', 'Reset link has expired (60 minutes). Please request a new one.');
    }
    
    return view('Admin.emails.reset_password', [
        'token' => $token,
        'email' => $email
    ]);
}

    // UPDATE PASSWORD
   public function updatePassword(Request $request)
{
    $request->validate([
        'token'    => 'required',
        'email'    => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $resetData = DB::table('password_reset_tokens')
        ->where('email', $request->email)
        ->where('token', $request->token)
        ->first();

    if (!$resetData) {
        return back()->with('error', 'Invalid reset token. Please request a new link.');
    }

    if (Carbon::parse($resetData->created_at)->addMinutes(60)->isPast()) {
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        return back()->with('error', 'Reset link has expired. Please request a new one.');
    }

    // Update password
    DB::table('admins')->where('email', $request->email)->update([
        'password' => Hash::make($request->password)
    ]);

    // Delete the token
    DB::table('password_reset_tokens')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('success', 'Password updated successfully! You can now login.');
}
    // LOGOUT
    public function logout(Request $request)
    {
        $request->session()->forget(['admin_email', 'admin_id', 'admin_name']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    // ============================================
    // MISSING METHODS FOR ROUTING (ADDED FOR COMPATIBILITY)
    // ============================================
    
    /**
     * Alias method for showLogin - used by route 'admin.login'
     */
    public function showLoginForm()
    {
        return $this->showLogin();
    }
    
    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('login');
        }
        return view('Admin.dashboard');
    }
}