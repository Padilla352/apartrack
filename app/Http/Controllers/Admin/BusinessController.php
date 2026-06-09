<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Business;

class BusinessController extends Controller
{
    /**
     * Display a listing of all businesses.
     */
    /**
 * Display a listing of all businesses (ONLY APPROVED).
 */
public function index()
{
    if (!session()->has('admin_email')) {
        return redirect()->route('admin.login');
    }

    // FIXED: Only show approved business spaces
    $businesses = DB::table('business_spaces')
        ->leftJoin('barangays', 'business_spaces.barangay_id', '=', 'barangays.id')
        ->leftJoin('owners', 'business_spaces.owner_id', '=', 'owners.id')
        ->select(
            'business_spaces.*', 
            'barangays.name as barangay_name',
            'owners.name as owner_name'
        )
        ->where('business_spaces.verification_status', 'approved')  // ← ADD THIS LINE
        ->orderBy('business_spaces.created_at', 'desc')
        ->paginate(15);

    $totalBusinesses = DB::table('business_spaces')->where('verification_status', 'approved')->count();  // ← FIXED
    $activeBusinesses = DB::table('business_spaces')->where('verification_status', 'approved')->where('status', 'Available')->count();  // ← FIXED
    $pendingVerifications = DB::table('business_spaces')->where('verification_status', 'pending')->count();
    $categoryCount = $this->getUniqueCategoryCount();

    return view('admin.business_listings.index', compact('businesses', 'totalBusinesses', 'activeBusinesses', 'pendingVerifications', 'categoryCount'));
}

    /**
     * Display the specified business.
     */
    public function show($id)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $business = DB::table('business_spaces')
            ->leftJoin('barangays', 'business_spaces.barangay_id', '=', 'barangays.id')
            ->leftJoin('owners', 'business_spaces.owner_id', '=', 'owners.id')
            ->select(
                'business_spaces.*',
                'barangays.name as barangay_name',
                'owners.name as owner_name',
                'owners.email as owner_email',
                'owners.phone as owner_phone'
            )
            ->where('business_spaces.id', $id)
            ->first();

        if (!$business) {
            return redirect()->route('admin.business.index')->with('error', 'Business not found.');
        }

        // Decode JSON fields
        if ($business->images) {
            $business->images = json_decode($business->images, true) ?: [];
        }
        if ($business->amenities) {
            $business->amenities = json_decode($business->amenities, true) ?: [];
        }
        if ($business->business_features) {
            $business->business_features = json_decode($business->business_features, true) ?: [];
        }

        return view('admin.business_listings.business_details', compact('business'));
    }

    /**
     * Show form for creating a new business.
     */
    public function create()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $barangays = DB::table('barangays')->orderBy('name')->get();
        $types = ['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'];

        return view('admin.business_listings.create', compact('barangays', 'types'));
    }

    /**
     * Store a newly created business.
     */
    public function store(Request $request)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'unit_number' => 'nullable|string|max:50',
            'type' => 'required|in:Office,Retail,Restaurant,Warehouse,Co-working,Studio,Other',
            'monthly_rent' => 'required|numeric|min:0',
            'barangay_id' => 'required|exists:barangays,id',
            'address' => 'required|string',
            'floor_area_sqm' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'status' => 'required|in:Available,Occupied,Maintenance,Reserved',
            'amenities' => 'nullable|array',
            'business_features' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // Handle images
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('business_spaces', 'public');
                $imagePaths[] = $path;
            }
        }

        $data = [
            'business_name' => $validated['business_name'],
            'unit_number' => $validated['unit_number'] ?? null,
            'type' => $validated['type'],
            'monthly_rent' => $validated['monthly_rent'],
            'status' => $validated['status'],
            'barangay_id' => $validated['barangay_id'],
            'address' => $validated['address'],
            'floor_area_sqm' => $validated['floor_area_sqm'] ?? null,
            'description' => $validated['description'] ?? null,
            'amenities' => json_encode($validated['amenities'] ?? []),
            'business_features' => json_encode($validated['business_features'] ?? []),
            'images' => json_encode($imagePaths),
            'verification_status' => 'approved', // Admin created, auto-approved
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $id = DB::table('business_spaces')->insertGetId($data);

        return redirect()->route('admin.business.show', $id)
            ->with('success', 'Business space created successfully.');
    }

    /**
     * Show form for editing a business.
     */
    public function edit($id)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $business = DB::table('business_spaces')->where('id', $id)->first();

        if (!$business) {
            return redirect()->route('admin.business.index')->with('error', 'Business not found.');
        }

        // Decode JSON fields
        if ($business->images) {
            $business->images = json_decode($business->images, true) ?: [];
        }
        if ($business->amenities) {
            $business->amenities = json_decode($business->amenities, true) ?: [];
        }
        if ($business->business_features) {
            $business->business_features = json_decode($business->business_features, true) ?: [];
        }

        $barangays = DB::table('barangays')->orderBy('name')->get();
        $types = ['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'];

        return view('admin.business_listings.edit', compact('business', 'barangays', 'types'));
    }

    /**
     * Update the specified business.
     */
    public function update(Request $request, $id)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'unit_number' => 'nullable|string|max:50',
            'type' => 'required|in:Office,Retail,Restaurant,Warehouse,Co-working,Studio,Other',
            'monthly_rent' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Occupied,Maintenance,Reserved',
            'barangay_id' => 'required|exists:barangays,id',
            'address' => 'required|string',
            'floor_area_sqm' => 'nullable|integer',
            'description' => 'nullable|string',
            'amenities' => 'nullable|array',
            'business_features' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'removed_images' => 'nullable|string',
        ]);

        // Get current images
        $business = DB::table('business_spaces')->where('id', $id)->first();
        $currentImages = $business && $business->images ? json_decode($business->images, true) : [];
        
        // Handle removed images
        if ($request->filled('removed_images')) {
            $removed = json_decode($request->removed_images, true);
            if (is_array($removed)) {
                foreach ($removed as $oldPath) {
                    \Storage::disk('public')->delete($oldPath);
                }
                $currentImages = array_diff($currentImages, $removed);
            }
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('business_spaces', 'public');
                $currentImages[] = $path;
            }
        }

        $data = [
            'business_name' => $validated['business_name'],
            'unit_number' => $validated['unit_number'] ?? null,
            'type' => $validated['type'],
            'monthly_rent' => $validated['monthly_rent'],
            'status' => $validated['status'],
            'barangay_id' => $validated['barangay_id'],
            'address' => $validated['address'],
            'floor_area_sqm' => $validated['floor_area_sqm'] ?? null,
            'description' => $validated['description'] ?? null,
            'amenities' => json_encode($validated['amenities'] ?? []),
            'business_features' => json_encode($validated['business_features'] ?? []),
            'images' => json_encode(array_values($currentImages)),
            'updated_at' => now(),
        ];

        DB::table('business_spaces')->where('id', $id)->update($data);

        return redirect()->route('admin.business.show', $id)
            ->with('success', 'Business space updated successfully.');
    }

    /**
     * Remove the specified business.
     */
    public function destroy($id)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        // Delete images from storage
        $business = DB::table('business_spaces')->where('id', $id)->first();
        if ($business && $business->images) {
            $images = json_decode($business->images, true);
            if (is_array($images)) {
                foreach ($images as $imagePath) {
                    \Storage::disk('public')->delete($imagePath);
                }
            }
        }

        DB::table('business_spaces')->where('id', $id)->delete();

        return redirect()->route('admin.business.index')
            ->with('success', 'Business space deleted successfully.');
    }

    /**
     * Display pending business spaces for verification.
     */
    public function pendingVerifications()
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $pendingBusinesses = DB::table('business_spaces')
            ->leftJoin('owners', 'business_spaces.owner_id', '=', 'owners.id')
            ->leftJoin('barangays', 'business_spaces.barangay_id', '=', 'barangays.id')
            ->select(
                'business_spaces.*',
                'owners.name as owner_name',
                'owners.email as owner_email',
                'owners.phone as owner_phone',
                'barangays.name as barangay_name'
            )
            ->where('business_spaces.verification_status', 'pending')
            ->orderBy('business_spaces.created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_pending' => DB::table('business_spaces')->where('verification_status', 'pending')->count(),
            'total_approved' => DB::table('business_spaces')->where('verification_status', 'approved')->count(),
            'total_rejected' => DB::table('business_spaces')->where('verification_status', 'rejected')->count(),
            'approved_this_month' => DB::table('business_spaces')
                ->where('verification_status', 'approved')
                ->whereMonth('verified_at', Carbon::now()->month)
                ->count(),
        ];

        return view('admin.business_listings.pending', compact('pendingBusinesses', 'stats'));
    }

    /**
     * Approve a business space listing.
     */
    public function approveVerification($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $business = DB::table('business_spaces')->where('id', $id)->first();

            if (!$business) {
                return response()->json(['success' => false, 'message' => 'Business space not found.']);
            }

            DB::table('business_spaces')
                ->where('id', $id)
                ->update([
                    'verification_status' => 'approved',
                    'verified_at' => now(),
                    'verified_by' => session('admin_id'),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Business space approved successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Approve business error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reject a business space listing.
     */
    public function rejectVerification(Request $request, $id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:10',
        ]);

        try {
            $business = DB::table('business_spaces')->where('id', $id)->first();

            if (!$business) {
                return response()->json(['success' => false, 'message' => 'Business space not found.']);
            }

            DB::table('business_spaces')
                ->where('id', $id)
                ->update([
                    'verification_status' => 'rejected',
                    'rejection_reason' => $request->rejection_reason,
                    'verified_at' => now(),
                    'verified_by' => session('admin_id'),
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Business space rejected.'
            ]);

        } catch (\Exception $e) {
            Log::error('Reject business error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display approved business spaces by barangay.
     */
    public function showBarangay($slug)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $barangayName = ucwords(str_replace('-', ' ', $slug));
        
        $barangay = DB::table('barangays')->where('name', $barangayName)->first();
        
        if (!$barangay) {
            return redirect()->route('admin.business.index')->with('error', 'Barangay not found.');
        }

        $businesses = DB::table('business_spaces')
            ->leftJoin('owners', 'business_spaces.owner_id', '=', 'owners.id')
            ->select('business_spaces.*', 'owners.name as owner_name')
            ->where('business_spaces.barangay_id', $barangay->id)
            ->where('business_spaces.verification_status', 'approved')
            ->orderBy('business_spaces.created_at', 'desc')
            ->paginate(12);

        return view('admin.business_listings.business_list', compact('barangayName', 'businesses'));
    }

    /**
     * Approve Owner Business (for unified verification)
     */
    public function approveOwner($id)
    {
        if (!session()->has('admin_email')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $business = DB::table('business_spaces')->where('id', $id)->first();

            if (!$business) {
                return response()->json(['success' => false, 'message' => 'Business space not found.']);
            }

            DB::table('business_spaces')
                ->where('id', $id)
                ->update([
                    'verification_status' => 'approved',
                    'verified_at' => now(),
                    'verified_by' => session('admin_id'),
                    'updated_at' => now(),
                ]);

            // Also update the permit number in permit_numbers table if needed
            if ($business->permit_number) {
                DB::table('permit_numbers')
                    ->where('permit_number', $business->permit_number)
                    ->update([
                        'status' => 'used',
                        'used_at' => now(),
                        'updated_at' => now(),
                    ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Business space approved successfully!'
            ]);

        } catch (\Exception $e) {
            Log::error('Approve owner business error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Safely count records from a table.
     */
    private function safeCount($table, $column = null, $value = null)
    {
        try {
            if (Schema::hasTable($table)) {
                if ($column && Schema::hasColumn($table, $column)) {
                    return DB::table($table)->where($column, $value)->count();
                }
                return DB::table($table)->count();
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get unique category count.
     */
    private function getUniqueCategoryCount()
    {
        try {
            if (Schema::hasTable('business_spaces')) {
                return DB::table('business_spaces')->distinct('type')->count('type');
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Search businesses.
     */
    public function search(Request $request)
    {
        if (!session()->has('admin_email')) {
            return redirect()->route('admin.login');
        }

        $query = $request->get('q');
        
        $businesses = DB::table('business_spaces')
            ->where('business_name', 'LIKE', "%{$query}%")
            ->orWhere('type', 'LIKE', "%{$query}%")
            ->orWhere('address', 'LIKE', "%{$query}%")
            ->paginate(12);
        
        return view('admin.business_listings.search_results', compact('businesses', 'query'));
    }
}