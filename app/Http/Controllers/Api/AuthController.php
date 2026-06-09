<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Send OTP (step 1 of registration)
    public function sendOtp(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        // Store pending registration
        DB::table('pending_registrations')->updateOrInsert(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'created_at' => now(),
            ]
        );

        // Generate and store OTP
        $otpCode = rand(100000, 999999);
        Otp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otpCode, 'expires_at' => now()->addMinutes(10)]
        );

        // Send email (use log driver for testing)
        Mail::to($request->email)->send(new OtpMail($otpCode, $request->email));

        return response()->json(['message' => 'OTP sent']);
    }

    // Verify OTP (step 2 of registration)
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $otpInput = (string) $request->otp;

        // otps table stores otp as VARCHAR, but Flutter sometimes sends it as int/string.
        // Normalize and check both normalized representations.
        $otpRecord = Otp::where('email', $request->email)
                        ->where(function ($q) use ($otpInput) {
                            $q->where('otp', $otpInput)
                              ->orWhere('otp', (int) $otpInput);
                        })
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            $notExpiredAnyForEmail = Otp::where('email', $request->email)
                ->where('expires_at', '>', now())
                ->count();

            $matchingOtpAny = Otp::where('email', $request->email)
                ->where(function ($q) use ($otpInput) {
                    $q->where('otp', $otpInput)
                      ->orWhere('otp', (int) $otpInput);
                })
                ->count();

            // last stored OTP for this email (helps diagnose exact formatting)
            $lastOtp = Otp::where('email', $request->email)
                ->orderByDesc('id')
                ->first(['otp', 'expires_at']);

            return response()->json([
                'message' => 'Invalid or expired OTP',
                'debug' => [
                    'email' => $request->email,
                    'otp_provided' => $request->otp,
                    'otp_count_not_expired_for_email' => $notExpiredAnyForEmail,
                    'otp_count_matching_value_any_expiry' => $matchingOtpAny,
                    'last_otp_in_db' => $lastOtp ? [
                        'otp' => $lastOtp->otp,
                        'expires_at' => $lastOtp->expires_at,
                    ] : null,
                ]
            ], 422);
        }

        $pending = DB::table('pending_registrations')->where('email', $request->email)->first();
        if (!$pending) {
            return response()->json(['message' => 'Session expired'], 422);
        }

        $user = User::create([
            'name' => $pending->name,
            'email' => $pending->email,
            'password' => $pending->password,
            'email_verified_at' => now(),
        ]);

        $otpRecord->delete();
        DB::table('pending_registrations')->where('email', $request->email)->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->only('id', 'name', 'email'),
            'token' => $token,
        ]);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages(['email' => 'Invalid credentials']);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user->only('id', 'name', 'email'),
            'token' => $token,
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}