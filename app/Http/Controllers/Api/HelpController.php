<?php

namespace App\Http\Controllers\Api;

use App\Models\Faq;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;

class HelpController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        $faqs = Faq::where('is_active', true)->orderBy('order')->get();
        return view('help', compact('faqs'));
    }

    public function storeFeedback(Request $request)
    {
        // Rate limit: max 3 submissions per email per 10 minutes
        $key = 'feedback_' . $request->ip() . '_' . $request->email;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            return response()->json([
                'success' => false,
                'message' => 'Too many attempts. Please wait before sending more feedback.'
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|max:255',
            'issue_type' => 'required|string|in:general,technical,billing,feature',
            'message'    => 'required|string|min:10|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Duplicate prevention: same email and message within last 5 minutes
        $recent = Feedback::where('email', $request->email)
            ->where('message', $request->message)
            ->where('created_at', '>=', now()->subMinutes(5))
            ->exists();

        if ($recent) {
            return response()->json([
                'success' => false,
                'message' => 'You have already submitted the same feedback recently. Please wait a few minutes.'
            ], 429);
        }

        $feedback = Feedback::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'issue_type' => $request->issue_type,
            'message'    => $request->message,
            'ip_address' => $request->ip(),
        ]);

        RateLimiter::hit($key, 600); // 10 minutes

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback! We will get back to you soon.'
        ]);
    }
}