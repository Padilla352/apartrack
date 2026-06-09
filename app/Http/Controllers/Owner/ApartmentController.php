<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Business;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class ApartmentController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Get the owner to access property_type
        $owner = Owner::find($ownerId);
        $propertyType = $owner->property_type ?? 'apartment';
        
        $application = DB::table('permit_applications')
            ->where('user_id', $ownerId)
            ->first();
        
        $isVerified = $application && $application->status === 'approved';
        $verificationStatus = $application->status ?? 'pending';
        
        // Get ALL listings (apartments + businesses)
        $apartments = Apartment::where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $businesses = Business::where('owner_id', $ownerId)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Merge and sort by created_at
        $allListings = $apartments->concat($businesses)->sortByDesc('created_at');
        
        // Paginate manually
        $perPage = 10;
        $currentPage = $request->get('page', 1);
        $currentItems = $allListings->slice(($currentPage - 1) * $perPage, $perPage);
        $listings = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems, 
            $allListings->count(), 
            $perPage, 
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        // Pass propertyType to the view
        return view('owner.apartments.index', compact('listings', 'isVerified', 'verificationStatus', 'propertyType'));
    }

    public function create()
    {
        $ownerId = Auth::guard('owner')->id();
        
        $owner = Owner::find($ownerId);
        $registeredPermitNumber = $owner->permit_number ?? '';
        
        $application = DB::table('permit_applications')
            ->where('user_id', $ownerId)
            ->where('status', 'approved')
            ->first();
        
        if (!$application) {
            return redirect()->route('owner.apartments.index')
                ->with('error', 'Your account is pending admin approval. You cannot add listings yet.');
        }
        
        $barangays = DB::table('barangays')->orderBy('name')->get();
        return view('owner.apartments.create', compact('barangays', 'registeredPermitNumber'));
    }

    public function store(Request $request)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            $owner = Owner::find($ownerId);
            $registeredPermitNumber = $owner->permit_number;
            
            $application = DB::table('permit_applications')
                ->where('user_id', $ownerId)
                ->where('status', 'approved')
                ->first();
            
            if (!$application) {
                return redirect()->route('owner.apartments.index')
                    ->with('error', 'Your account is pending admin approval.');
            }
            
            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'unit_number'     => 'required|string|max:50',
                'type'            => 'required|string|max:50',
                'bedrooms'        => 'nullable|integer|min:0|max:10',
                'bathrooms'       => 'nullable|integer|min:0|max:10',
                'monthly_rent'    => 'required|numeric|min:0',
                'floor_area_sqm'  => 'nullable|numeric|min:0',
                'description'     => 'nullable|string',
                'address'         => 'required|string',
                'barangay_name'   => 'required|string',
                'permit_number'   => 'nullable|string|max:50',
                'status'          => 'in:Vacant,Reserved,Occupied',
                'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'latitude'        => 'nullable|numeric|between:-90,90',
                'longitude'       => 'nullable|numeric|between:-180,180',
            ]);

            $permitNumber = $registeredPermitNumber;

            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image && $image->isValid()) {
                        $path = $image->store('apartments', 'public');
                        $imagePaths[] = $path;
                    }
                }
            }

            // --- AUTO GEOCODE if coordinates missing ---
            $latitude = $validated['latitude'] ?? null;
            $longitude = $validated['longitude'] ?? null;
            if ((empty($latitude) || empty($longitude)) && !empty($validated['address'])) {
                $fullAddress = $validated['address'] . ', ' . $validated['barangay_name'] . ', Binalonan, Pangasinan, Philippines';
                $coords = $this->geocodeAddress($fullAddress);
                if ($coords) {
                    $latitude = $coords['lat'];
                    $longitude = $coords['lng'];
                }
            }

            $apartment = Apartment::create([
                'owner_id'           => $ownerId,
                'name'               => $validated['name'],
                'unit_number'        => $validated['unit_number'],
                'type'               => $validated['type'],
                'bedrooms'           => $validated['bedrooms'] ?? null,
                'bathrooms'          => $validated['bathrooms'] ?? null,
                'monthly_rent'       => $validated['monthly_rent'],
                'floor_area_sqm'     => $validated['floor_area_sqm'] ?? null,
                'description'        => $validated['description'] ?? null,
                'address'            => $validated['address'],
                'barangay_name'      => $validated['barangay_name'],
                'permit_number'      => $permitNumber,
                'status'             => $validated['status'] ?? 'Vacant',
                'verification_status'=> 'pending',
                'images'             => json_encode($imagePaths),
                'latitude'           => $latitude,
                'longitude'          => $longitude,
            ]);

            return redirect()->route('owner.apartments.index')
                ->with('success', '✅ Apartment listing submitted for admin verification.');
                
        } catch (\Exception $e) {
            Log::error('Store error: ' . $e->getMessage());
            return redirect()->route('owner.apartments.index')
                ->with('error', 'Failed to add listing: ' . $e->getMessage());
        }
    }

    public function show(int|string $id)
    {
        $apartment = Apartment::where('owner_id', Auth::guard('owner')->id())
            ->findOrFail($id);
        return view('owner.apartments.show', compact('apartment'));
    }

    public function edit(int|string $id)
    {
        $ownerId = Auth::guard('owner')->id();
        
        $owner = Owner::find($ownerId);
        $registeredPermitNumber = $owner->permit_number ?? '';
        
        $application = DB::table('permit_applications')
            ->where('user_id', $ownerId)
            ->where('status', 'approved')
            ->first();
        
        if (!$application) {
            return redirect()->route('owner.apartments.index')
                ->with('error', 'Your account is pending admin approval.');
        }
        
        $apartment = Apartment::where('owner_id', $ownerId)
            ->findOrFail($id);

        $barangays = DB::table('barangays')->orderBy('name')->get();

        return view('owner.apartments.edit', compact('apartment', 'barangays', 'registeredPermitNumber'));
    }

    public function update(Request $request, int|string $id)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            $owner = Owner::find($ownerId);
            $registeredPermitNumber = $owner->permit_number;
            
            $application = DB::table('permit_applications')
                ->where('user_id', $ownerId)
                ->where('status', 'approved')
                ->first();
            
            if (!$application) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Account pending approval.'], 403);
                }
                return redirect()->route('owner.apartments.index')
                    ->with('error', 'Your account is pending admin approval.');
            }
            
            $apartment = Apartment::where('owner_id', $ownerId)
                ->findOrFail($id);

            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'address'         => 'required|string',
                'monthly_rent'    => 'required|numeric|min:0',
                'description'     => 'nullable|string',
                'type'            => 'required|string',
                'bedrooms'        => 'nullable|integer',
                'bathrooms'       => 'nullable|integer',
                'floor_area_sqm'  => 'nullable|numeric',
                'unit_number'     => 'required|string',
                'status'          => 'required|string',
                'amenities'       => 'nullable|array',
                'new_images.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
                'removed_images'  => 'nullable|string',
                'latitude'        => 'nullable|numeric|between:-90,90',
                'longitude'       => 'nullable|numeric|between:-180,180',
            ]);

            $currentImages = [];
            if ($apartment->images) {
                if (is_string($apartment->images)) {
                    $currentImages = json_decode($apartment->images, true) ?: [];
                } elseif (is_array($apartment->images)) {
                    $currentImages = $apartment->images;
                }
            }
            
            if ($request->filled('removed_images')) {
                $removed = json_decode($request->removed_images, true);
                if (is_array($removed)) {
                    foreach ($removed as $oldPath) {
                        Storage::disk('public')->delete($oldPath);
                    }
                    $currentImages = array_diff($currentImages, $removed);
                }
            }

            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $image) {
                    $path = $image->store('apartments', 'public');
                    $currentImages[] = $path;
                }
            }

            // --- AUTO GEOCODE if address changed or coordinates missing ---
            $latitude = $validated['latitude'] ?? null;
            $longitude = $validated['longitude'] ?? null;
            $addressChanged = ($validated['address'] !== $apartment->address) || ($validated['barangay_name'] !== $apartment->barangay_name);
            
            if ((empty($latitude) || empty($longitude)) && !empty($validated['address']) && $addressChanged) {
                $fullAddress = $validated['address'] . ', ' . $validated['barangay_name'] . ', Binalonan, Pangasinan, Philippines';
                $coords = $this->geocodeAddress($fullAddress);
                if ($coords) {
                    $latitude = $coords['lat'];
                    $longitude = $coords['lng'];
                }
            } elseif (empty($latitude) || empty($longitude)) {
                // Keep existing coordinates if available and not changed
                $latitude = $apartment->latitude;
                $longitude = $apartment->longitude;
            }

            $apartment->update([
                'name'           => $validated['name'],
                'address'        => $validated['address'],
                'monthly_rent'   => $validated['monthly_rent'],
                'description'    => $validated['description'],
                'type'           => $validated['type'],
                'bedrooms'       => $validated['bedrooms'],
                'bathrooms'      => $validated['bathrooms'],
                'floor_area_sqm' => $validated['floor_area_sqm'],
                'unit_number'    => $validated['unit_number'],
                'status'         => $validated['status'],
                'amenities'      => $validated['amenities'] ?? [],
                'images'         => json_encode(array_values($currentImages)),
                'latitude'       => $latitude,
                'longitude'      => $longitude,
            ]);

            if ($apartment->permit_number != $registeredPermitNumber) {
                $apartment->update(['permit_number' => $registeredPermitNumber]);
            }

            return response()->json(['success' => true, 'message' => 'Property updated successfully!']);
            
        } catch (\Exception $e) {
            Log::error('Update error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy(int|string $id)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            $application = DB::table('permit_applications')
                ->where('user_id', $ownerId)
                ->where('status', 'approved')
                ->first();
            
            if (!$application) {
                return redirect()->route('owner.apartments.index')
                    ->with('error', 'Your account is pending admin approval.');
            }
            
            $apartment = Apartment::where('owner_id', $ownerId)
                ->findOrFail($id);

            $images = is_string($apartment->images) ? json_decode($apartment->images, true) : $apartment->images;
            if (is_array($images)) {
                foreach ($images as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            $apartment->delete();

            return redirect()->route('owner.apartments.index')
                ->with('success', 'Apartment deleted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('owner.apartments.index')
                ->with('error', 'Failed to delete listing.');
        }
    }

    public function revise($id)
    {
        $ownerId = Auth::guard('owner')->id();
        $apartment = Apartment::where('owner_id', $ownerId)
            ->where('verification_status', 'rejected')
            ->findOrFail($id);
        $barangays = DB::table('barangays')->orderBy('name')->get();
        return view('owner.apartments.revise', compact('apartment', 'barangays'));
    }

    public function resubmit(Request $request, $id)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            $apartment = Apartment::where('owner_id', $ownerId)
                ->where('verification_status', 'rejected')
                ->findOrFail($id);

            $validated = $request->validate([
                'name'            => 'required|string|max:255',
                'unit_number'     => 'required|string|max:50',
                'type'            => 'required|string|max:50',
                'bedrooms'        => 'nullable|integer|min:0|max:10',
                'bathrooms'       => 'nullable|integer|min:0|max:10',
                'monthly_rent'    => 'required|numeric|min:0',
                'floor_area_sqm'  => 'nullable|numeric|min:0',
                'description'     => 'nullable|string',
                'address'         => 'required|string',
                'barangay_name'   => 'required|string',
                'permit_number'   => 'nullable|string|max:50',
                'status'          => 'in:Vacant,Reserved,Occupied',
                'images.*'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
                'latitude'        => 'nullable|numeric|between:-90,90',
                'longitude'       => 'nullable|numeric|between:-180,180',
            ]);

            $currentImages = is_string($apartment->images) ? json_decode($apartment->images, true) : 
                             (is_array($apartment->images) ? $apartment->images : []);
            
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('apartments', 'public');
                    $currentImages[] = $path;
                }
            }

            // --- AUTO GEOCODE for resubmission ---
            $latitude = $validated['latitude'] ?? null;
            $longitude = $validated['longitude'] ?? null;
            if ((empty($latitude) || empty($longitude)) && !empty($validated['address'])) {
                $fullAddress = $validated['address'] . ', ' . $validated['barangay_name'] . ', Binalonan, Pangasinan, Philippines';
                $coords = $this->geocodeAddress($fullAddress);
                if ($coords) {
                    $latitude = $coords['lat'];
                    $longitude = $coords['lng'];
                }
            }

            $apartment->update([
                'name'               => $validated['name'],
                'unit_number'        => $validated['unit_number'],
                'type'               => $validated['type'],
                'bedrooms'           => $validated['bedrooms'] ?? null,
                'bathrooms'          => $validated['bathrooms'] ?? null,
                'monthly_rent'       => $validated['monthly_rent'],
                'floor_area_sqm'     => $validated['floor_area_sqm'] ?? null,
                'description'        => $validated['description'] ?? null,
                'address'            => $validated['address'],
                'barangay_name'      => $validated['barangay_name'],
                'permit_number'      => $validated['permit_number'] ?? $apartment->permit_number,
                'status'             => $validated['status'] ?? 'Vacant',
                'images'             => json_encode(array_values($currentImages)),
                'verification_status' => 'pending',
                'rejection_reason'    => null,
                'verified_at'         => null,
                'verified_by'         => null,
                'latitude'            => $latitude,
                'longitude'           => $longitude,
            ]);

            return redirect()->route('owner.apartments.index')
                ->with('success', '✅ Apartment resubmitted for admin review.');
                
        } catch (\Exception $e) {
            return redirect()->route('owner.apartments.index')
                ->with('error', 'Failed to resubmit listing.');
        }
    }

    /**
     * Convert an address to latitude and longitude using Google Geocoding API.
     *
     * @param string $address Full address string
     * @return array|null Associative array with 'lat' and 'lng' or null on failure
     */
    protected function geocodeAddress($address)
    {
        $apiKey = config('services.google_maps.key');
        
        if (!$apiKey) {
            Log::warning('Google Maps API key is missing. Please set GOOGLE_MAPS_API_KEY in .env');
            return null;
        }

        try {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => $apiKey,
            ]);

            if ($response->successful() && $response->json('status') === 'OK' && count($response->json('results'))) {
                $location = $response->json('results.0.geometry.location');
                return [
                    'lat' => $location['lat'],
                    'lng' => $location['lng']
                ];
            } else {
                Log::warning('Geocoding failed for address: ' . $address . ' | Status: ' . $response->json('status'));
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Geocoding exception: ' . $e->getMessage());
            return null;
        }
    }
}