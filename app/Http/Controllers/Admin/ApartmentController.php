<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ApartmentController extends Controller
{
    /**
     * Display list of all barangays.
     */
    public function barangay()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $barangays = DB::table('barangays')
            ->orderBy('name')
            ->get();
        
        if ($barangays->isEmpty()) {
            $barangays = collect();
        }

        return view('admin.apartment_listings.index', compact('barangays'));
    }

    /**
     * Display apartments in a specific barangay (ONLY APPROVED).
     */
    public function showBarangay($slug)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $barangayName = ucwords(str_replace('-', ' ', $slug));
        
        $apartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select(
                'apartments.*',
                'owners.name as owner_name',
                'owners.email as owner_email'
            )
            ->where('apartments.barangay_name', $barangayName)
            ->where('apartments.verification_status', 'approved')
            ->orderBy('apartments.created_at', 'desc')
            ->paginate(9);
        
        return view('admin.apartment_listings.apartment_list', compact('barangayName', 'apartments'));
    }

    /**
     * Display detailed view of a specific apartment (ONLY APPROVED).
     */
    public function viewApartmentDetails($id, Request $request)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $barangayName = $request->query('barangay', '');
        $barangaySlug = Str::slug($barangayName);
        
        $apartment = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select(
                'apartments.*',
                'owners.name as owner_name',
                'owners.email as owner_email',
                'owners.phone as owner_phone'
            )
            ->where('apartments.id', $id)
            ->where('apartments.verification_status', 'approved')
            ->first();
        
        if (!$apartment) {
            return redirect()->route('admin.apartments.barangay')
                ->with('error', 'Apartment not found or pending approval.');
        }
        
        $owner = null;
        if (isset($apartment->owner_id)) {
            $owner = DB::table('owners')
                ->where('id', $apartment->owner_id)
                ->first();
        }

        return view('admin.apartment_listings.apartment_details', compact(
            'id', 
            'barangayName', 
            'barangaySlug', 
            'apartment', 
            'owner'
        ));
    }

    /**
     * AJAX endpoint for loading more apartments (infinite scroll) - ONLY APPROVED.
     */
    public function loadMoreApartments(Request $request)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $barangayName = $request->query('barangay');
        $page = $request->query('page', 1);
        $perPage = 6;
        
        $apartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select('apartments.*', 'owners.name as owner_name')
            ->where('apartments.barangay_name', $barangayName)
            ->where('apartments.verification_status', 'approved')
            ->orderBy('apartments.created_at', 'desc')
            ->skip(($page - 1) * $perPage)
            ->take($perPage + 1)
            ->get();
        
        $hasMore = $apartments->count() > $perPage;
        $apartments = $apartments->take($perPage);
        
        $html = view('admin.apartment_listings.partials.apartment_cards', compact('apartments', 'barangayName'))->render();
        
        return response()->json([
            'html' => $html,
            'has_more' => $hasMore,
            'next_page' => $page + 1
        ]);
    }

    /**
     * Display pending verifications.
     */
    public function pendingVerifications()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $pendingApartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->leftJoin('permit_numbers', 'apartments.permit_number', '=', 'permit_numbers.permit_number')
            ->select(
                'apartments.*',
                'owners.name as owner_name',
                'owners.email as owner_email',
                'owners.phone as owner_phone',
                'permit_numbers.status as permit_status',
                'permit_numbers.id as permit_id',
                DB::raw('CASE WHEN permit_numbers.id IS NOT NULL AND permit_numbers.status = "active" THEN 1 ELSE 0 END as can_approve')
            )
            ->where('apartments.verification_status', 'pending')
            ->orderBy('apartments.created_at', 'desc')
            ->paginate(10);

        return view('admin.apartments.pending_verifications', compact('pendingApartments'));
    }

    /**
     * Approve an apartment listing.
     */
    public function approveVerification($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $apartment = DB::table('apartments')->where('id', $id)->first();

        if (!$apartment) {
            return response()->json(['success' => false, 'message' => 'Apartment not found.']);
        }

        DB::table('apartments')
            ->where('id', $id)
            ->update([
                'verification_status' => 'approved',
                'verified_at' => now(),
                'verified_by' => session('admin_id'),
                'updated_at' => now(),
            ]);

        return response()->json([
            'success' => true, 
            'message' => '✅ Apartment listing approved and now visible to users!'
        ]);
    }

    /**
     * Reject an apartment listing.
     */
    public function rejectVerification(Request $request, $id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        $apartment = DB::table('apartments')->where('id', $id)->first();

        if (!$apartment) {
            return response()->json(['success' => false, 'message' => 'Apartment not found.']);
        }

        DB::table('apartments')
            ->where('id', $id)
            ->update([
                'verification_status' => 'rejected',
                'rejection_reason' => $request->rejection_reason,
                'verified_at' => now(),
                'verified_by' => session('admin_id'),
                'updated_at' => now(),
            ]);

        return response()->json(['success' => true, 'message' => '❌ Apartment listing rejected.']);
    }

    /**
     * Display all approved apartments (for management).
     */
    public function approvedListings()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $approvedApartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select(
                'apartments.*',
                'owners.name as owner_name',
                'owners.email as owner_email'
            )
            ->where('apartments.verification_status', 'approved')
            ->orderBy('apartments.verified_at', 'desc')
            ->paginate(15);

        return view('admin.apartments.approved_listings', compact('approvedApartments'));
    }

    /**
     * Display rejected apartments.
     */
    public function rejectedListings()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $rejectedApartments = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select(
                'apartments.*',
                'owners.name as owner_name',
                'owners.email as owner_email'
            )
            ->where('apartments.verification_status', 'rejected')
            ->orderBy('apartments.verified_at', 'desc')
            ->paginate(15);

        return view('admin.apartments.rejected_listings', compact('rejectedApartments'));
    }
}