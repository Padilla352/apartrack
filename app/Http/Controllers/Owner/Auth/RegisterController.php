<?php

namespace App\Http\Controllers\Owner\Auth;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Otp;
use App\Models\Notification;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('owner.auth.register'); 
    }

    public function register(Request $request)
    {
        // Validate based on property type
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:owners',
            'phone' => 'nullable|string|max:20',
            'property_type' => 'required|in:apartment,both',  // NO BUSINESS-ONLY
            'password' => 'required|string|min:8|confirmed',
        ];
        
        // Add permit validation rules based on property type
        if ($request->property_type == 'apartment') {
            $rules['residential_permit'] = 'required|string|max:100';
        } elseif ($request->property_type == 'both') {
            $rules['residential_permit'] = 'required|string|max:100';
            $rules['business_permit'] = 'required|string|max:100';
        }
        
        $request->validate($rules);
        
        // Check if email already has a pending application
        $pendingApplication = DB::table('permit_applications')
            ->where('email', $request->email)
            ->where('status', 'pending')
            ->first();

        if ($pendingApplication) {
            return back()->withErrors([
                'email' => 'This email already has a pending registration. Please wait for admin approval.'
            ])->withInput($request->except('password', 'password_confirmation'));
        }
        
        // Store registration data in session
        session([
            'owner_registration' => [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'property_type' => $request->property_type,
                'residential_permit' => $request->residential_permit,
                'business_permit' => $request->business_permit,
                'password' => Hash::make($request->password),
            ]
        ]);

        // Generate OTP
        $otpCode = rand(100000, 999999);
        
        DB::table('otps')->where('email', $request->email)->delete();

        Otp::create([
            'email' => $request->email,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(10)
        ]);

        try {
            Mail::to($request->email)->send(new OtpMail($otpCode, $request->email));
        } catch (\Exception $e) {
            Log::error("Mail Error: " . $e->getMessage());
        }

        Log::info("OTP for {$request->email}: {$otpCode}");

        session(['email' => $request->email]);

        return redirect()->route('owner.verify.show');
    }

    public function showVerificationForm()
    {
        $email = session('email');
        
        if (!$email) {
            return redirect()->route('owner.register')->withErrors(['email' => 'Please register first.']);
        }

        return view('owner.auth.verify-otp', compact('email'));
    }

    public function verifyOTP(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        $otpRecord = Otp::where('email', $request->email)
                        ->where('otp', $request->otp)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP. Please try again.']);
        }

        $ownerData = session('owner_registration');
        if (!$ownerData) {
            return redirect()->route('owner.register')->withErrors(['email' => 'Registration session expired. Please register again.']);
        }

        $existingOwner = Owner::where('email', $ownerData['email'])->first();
        
        if ($existingOwner) {
            DB::table('otps')->where('email', $ownerData['email'])->delete();
            DB::table('permit_applications')->where('email', $ownerData['email'])->delete();
            session()->forget('owner_registration');
            session()->forget('email');
            
            return redirect()->route('owner.register')->withErrors([
                'email' => 'This email is already registered. Please use a different email or login instead.'
            ]);
        }

        $existingApplication = DB::table('permit_applications')
            ->where('email', $ownerData['email'])
            ->first();

        if ($existingApplication) {
            DB::table('otps')->where('email', $ownerData['email'])->delete();
            session()->forget('owner_registration');
            session()->forget('email');
            
            return redirect()->route('owner.register')->withErrors([
                'email' => 'This email already has a registration application. Please wait for admin approval.'
            ]);
        }

        // Determine primary permit number
        $primaryPermit = $ownerData['residential_permit'];
        $permitNumbers = [];

        if ($ownerData['residential_permit']) {
            $permitNumbers['residential'] = $ownerData['residential_permit'];
        }
        if ($ownerData['business_permit']) {
            $permitNumbers['business'] = $ownerData['business_permit'];
        }

        // Create the owner record
        $owner = Owner::create([
            'name' => $ownerData['name'],
            'email' => $ownerData['email'],
            'phone' => $ownerData['phone'],
            'property_type' => $ownerData['property_type'],
            'residential_permit' => $ownerData['residential_permit'] ?? null,
            'business_permit' => $ownerData['business_permit'] ?? null,
            'permit_number' => $primaryPermit,
            'password' => $ownerData['password'],
        ]);

        // Insert permit application(s)
        $permitTypes = [];
        if ($ownerData['residential_permit']) {
            $permitTypes[] = 'residential';
        }
        if ($ownerData['business_permit']) {
            $permitTypes[] = 'business';
        }

        foreach ($permitTypes as $type) {
            $permitNumber = $type == 'residential' ? $ownerData['residential_permit'] : $ownerData['business_permit'];
            
            DB::table('permit_applications')->insert([
                'permit_number' => $permitNumber,
                'permit_type' => $type,
                'user_id' => $owner->id,
                'applicant_name' => $ownerData['name'],
                'owner_name' => $ownerData['name'],
                'email' => $ownerData['email'],
                'property_name' => $ownerData['name'] . "'s Property",
                'business_name' => $type == 'business' ? $ownerData['name'] : '',
                'property_type' => $ownerData['property_type'],
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ========== CREATE NOTIFICATION FOR ADMIN ==========
        try {
            // Get property type text
            $propertyTypeText = $ownerData['property_type'] == 'apartment' ? 'Apartment Only' : 'Both (Apartment & Business)';
            
            // Create notification for admin
            Notification::create([
                'type' => 'owner_registration',
                'title' => 'New Owner Registration',
                'message' => $ownerData['name'] . ' has registered as a property owner (' . $propertyTypeText . ') and is pending approval.',
                'data' => json_encode([
                    'owner_id' => $owner->id,
                    'owner_name' => $ownerData['name'],
                    'owner_email' => $ownerData['email'],
                    'permit_number' => $primaryPermit,
                    'property_type' => $ownerData['property_type'],
                    'residential_permit' => $ownerData['residential_permit'] ?? null,
                    'business_permit' => $ownerData['business_permit'] ?? null,
                ]),
                'target_role' => 'admin',
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('Notification created for admin about new owner registration: ' . $ownerData['name']);
        } catch (\Exception $e) {
            Log::error('Failed to create notification: ' . $e->getMessage());
            // Don't stop registration if notification fails
        }

        $otpRecord->delete();
        session()->forget('owner_registration');
        session()->forget('email');

        return redirect()->route('owner.login')->with('success', 
            'Registration successful! Your application is pending admin approval. Please wait for verification.');
    }

    public function resendOTP(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $existingOwner = Owner::where('email', $request->email)->first();
        if ($existingOwner) {
            return back()->withErrors([
                'email' => 'This email is already registered. Please login instead.'
            ]);
        }

        $otpCode = rand(100000, 999999);
        
        DB::table('otps')->where('email', $request->email)->delete();

        Otp::create([
            'email' => $request->email,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(10)
        ]);

        try {
            Mail::to($request->email)->send(new OtpMail($otpCode, $request->email));
        } catch (\Exception $e) {
            Log::error("Resend Mail Error: " . $e->getMessage());
        }

        Log::info("Resent OTP for {$request->email}: {$otpCode}");

        return back()->with('success', 'A new OTP has been sent to your email.');
    }
}