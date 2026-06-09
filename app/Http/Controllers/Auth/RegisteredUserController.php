<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Otp;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; 

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('user.auth.register');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:8'],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => env('RECAPTCHA_SECRET_KEY'),
                    'response' => $value,
                    'remoteip' => request()->ip(),
                ]);

                if (!$response->json('success')) {
                    $fail('The reCAPTCHA verification failed. Please try again.');
                }
            }],
        ]);

        $otpCode = rand(100000, 999999);

        DB::table('otps')->where('email', $request->email)->delete();
        
        Otp::create([
            'email' => $request->email,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(10),
        ]);

        // INAYOS DITO: Idinagdag ang email bilang pangalawang argument
        Mail::to($request->email)->send(new OtpMail($otpCode, $request->email));

        session(['registration_data' => [
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]]);

        return redirect()->route('otp.verify.view');
    }

    public function showOtpForm()
    {
        if (!session()->has('registration_data')) {
            return redirect()->route('register');
        }
        return view('user.auth.verify-otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|numeric']);
        $data = session('registration_data');

        if (!$data) {
            return redirect()->route('register')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        $otpRecord = Otp::where('email', $data['email'])
                        ->where('otp', $request->otp)
                        ->where('expires_at', '>', now())
                        ->first();

        if (!$otpRecord) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP.']);
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $otpRecord->delete();
        session()->forget('registration_data');

        return redirect()->route('login')->with('success', 'Email verified successfully! You can now login.');
    }

    public function resendOtp(Request $request)
    {
        $data = session('registration_data');

        if (!$data) {
            return redirect()->route('register')->withErrors(['email' => 'Session expired.']);
        }

        $otpCode = rand(100000, 999999);

        DB::table('otps')->where('email', $data['email'])->delete();
        
        Otp::create([
            'email' => $data['email'],
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(10),
        ]);

        Mail::to($data['email'])->send(new OtpMail($otpCode, $data['email']));

        return back()->with('success', 'A new OTP has been sent to your email.');
    }
}