<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BusinessSpace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\BusinessInquiry;

class BusinessController extends Controller
{
    /**
     * Redirect to explore page (optional)
     */
    public function index(Request $request)
    {
        return redirect()->route('explore');
    }

    /**
     * Show business details using Eloquent (with relationships)
     */
    public function show($id)
    {
        Log::info('Business details requested', ['id' => $id]);

        // Use Eloquent para automatic na may owner relationship
        $business = BusinessSpace::with('owner')
            ->where('id', $id)
            ->where('verification_status', 'approved')
            ->first();

        if (!$business) {
            Log::warning('Business not found or not approved', ['id' => $id]);
            abort(404, 'Business space not found or not yet approved.');
        }

        // I-convert ang JSON fields sa arrays (kung naka-cast sa model, hindi na kailangan pero safe)
        if (is_string($business->images)) {
            $business->images = json_decode($business->images, true) ?: [];
        }
        if (is_string($business->amenities)) {
            $business->amenities = json_decode($business->amenities, true) ?: [];
        }
        if (is_string($business->business_features)) {
            $business->business_features = json_decode($business->business_features, true) ?: [];
        }

        // Tiyaking laging array ang images
        if (!is_array($business->images)) {
            $business->images = [];
        }

        // I-merge ang owner details sa business object para magamit sa blade
        if ($business->owner) {
            $business->owner_name = $business->owner->name;
            $business->owner_email = $business->owner->email;
            $business->owner_phone = $business->owner->phone;
            $business->owner_id = $business->owner->id;
        } else {
            $business->owner_name = null;
            $business->owner_email = null;
            $business->owner_phone = null;
            $business->owner_id = null;
        }

        // Siguraduhing may barangay_name (kung may relationship sa Barangay)
        if ($business->barangay) {
            $business->barangay_name = $business->barangay->name;
        }

        // Return the correct view
        return view('user.commercialspaces-details', compact('business'));
    }

    /**
     * Send a contact message to the business owner.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendContact(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|min:5',
        ]);

        // Find the business with its owner
        $business = BusinessSpace::with('owner')->find($id);

        if (!$business) {
            return back()->with('error', 'Business not found.');
        }

        // Determine recipient email (owner's email or fallback admin)
        $owner = $business->owner;
        $recipientEmail = $owner?->email ?? config('mail.admin_address', 'admin@example.com');

        // Send the email
        try {
            Mail::to($recipientEmail)->send(new BusinessInquiry($business, auth()->user(), $request->message));
            return back()->with('success', 'Your message has been sent to the business owner.');
        } catch (\Exception $e) {
            Log::error('Failed to send business inquiry email', ['error' => $e->getMessage()]);
            return back()->with('error', 'Unable to send message. Please try again later.');
        }
    }
}