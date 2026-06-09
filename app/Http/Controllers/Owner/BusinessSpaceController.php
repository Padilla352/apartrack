<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Barangay;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class BusinessSpaceController extends Controller
{
    public function index(Request $request)
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Check verification status
        $application = DB::table('permit_applications')
            ->where('user_id', $ownerId)
            ->first();
        
        $isVerified = $application && $application->status === 'approved';
        $verificationStatus = $application->status ?? 'pending';
        
        $query = Business::where('owner_id', $ownerId);
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('unit_number', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        
        $businessSpaces = $query->orderBy('created_at', 'desc')->paginate(10);
        $barangays = Barangay::orderBy('name')->get();
        
        $stats = [
            'total' => Business::where('owner_id', $ownerId)->count(),
            'occupied' => Business::where('owner_id', $ownerId)->where('status', 'Occupied')->count(),
            'available' => Business::where('owner_id', $ownerId)->where('status', 'Available')->count(),
            'maintenance' => Business::where('owner_id', $ownerId)->where('status', 'Maintenance')->count()
        ];
        
        $types = ['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'];
        
        return view('owner.business.index', compact('businessSpaces', 'barangays', 'stats', 'types', 'isVerified', 'verificationStatus'));
    }
    
    public function create()
    {
        $ownerId = Auth::guard('owner')->id();
        $owner = Owner::find($ownerId);
        
        // Get registered permit number from owner
        $registeredPermitNumber = $owner->permit_number ?? '';
        
        // Check if owner is verified
        $application = DB::table('permit_applications')
            ->where('user_id', $ownerId)
            ->where('status', 'approved')
            ->first();
        
        if (!$application) {
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'Your account is pending admin approval. You cannot add listings yet.');
        }
        
        $barangays = Barangay::orderBy('name')->get();
        $types = ['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'];
        $amenitiesList = ['Parking', '24/7 Security', 'CCTV', 'Backup Power', 'Elevator', 'Air Conditioning', 'Wifi', 'Conference Room', 'Kitchenette', 'Private Restroom'];
        $businessFeatures = ['Loading Bay', 'Delivery Access', 'Signage Space', 'Street Frontage', 'Corner Lot', 'Drive-thru Capable'];
        
        return view('owner.business.create', compact('barangays', 'types', 'amenitiesList', 'businessFeatures', 'registeredPermitNumber'));
    }
    
    public function store(Request $request)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            $owner = Owner::find($ownerId);
            
            // Get registered permit number from owner
            $registeredPermitNumber = $owner->permit_number;
            
            // Check if owner is verified
            $application = DB::table('permit_applications')
                ->where('user_id', $ownerId)
                ->where('status', 'approved')
                ->first();
            
            if (!$application) {
                return redirect()->route('owner.business-spaces.index')
                    ->with('error', 'Your account is pending admin approval.');
            }
            
            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'unit_number' => 'nullable|string|max:50',
                'type' => ['required', Rule::in(['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'])],
                'monthly_rent' => 'required|numeric|min:0',
                'barangay_name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'floor_area_sqm' => 'nullable|integer|min:0',
                'amenities' => 'nullable|array',
                'business_features' => 'nullable|array',
                'description' => 'nullable|string',
                'status' => ['required', Rule::in(['Available', 'Occupied', 'Maintenance', 'Reserved'])],
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
            
            DB::beginTransaction();
            
            // Get barangay ID from name
            $barangay = Barangay::where('name', $validated['barangay_name'])->first();
            
            // Handle multiple images
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    if ($image && $image->isValid()) {
                        $path = $image->store('business_spaces', 'public');
                        $imagePaths[] = $path;
                    }
                }
            }
            
            $business = Business::create([
                'owner_id' => $ownerId,
                'business_name' => $validated['business_name'],
                'unit_number' => $validated['unit_number'] ?? null,
                'type' => $validated['type'],
                'price' => $validated['monthly_rent'],
                'monthly_rent' => $validated['monthly_rent'],
                'floor_area_sqm' => $validated['floor_area_sqm'] ?? null,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'],
                'barangay_id' => $barangay->id ?? null,
                'barangay_name' => $validated['barangay_name'],
                'permit_number' => $registeredPermitNumber,
                'status' => $validated['status'],
                'verification_status' => 'pending',
                'images' => $imagePaths,
                'amenities' => $validated['amenities'] ?? [],
                'business_features' => $validated['business_features'] ?? [],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);
            
            DB::commit();
            
            return redirect()->route('owner.business-spaces.index')
                ->with('success', 'Business space submitted for admin verification!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Store business error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to add business space. Error: ' . $e->getMessage());
        }
    }
    
    public function show($id)
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Find business by ID with error handling
        $business = Business::find($id);
        
        // Check if business exists
        if (!$business) {
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'Business space not found.');
        }
        
        // Log the values for debugging
        Log::info('Business Show Debug', [
            'business_id' => $id,
            'business_owner_id' => $business->owner_id,
            'current_owner_id' => $ownerId,
            'owner_email' => Auth::guard('owner')->user()->email
        ]);
        
        // Check if business has owner_id
        if (is_null($business->owner_id)) {
            Log::warning('Business has null owner_id', ['business_id' => $id]);
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'This business record has no owner assigned. Please contact support.');
        }
        
        // Verify ownership
        if ($business->owner_id != $ownerId) {
            Log::warning('Owner mismatch', [
                'business_owner' => $business->owner_id,
                'current_owner' => $ownerId,
                'user_email' => Auth::guard('owner')->user()->email
            ]);
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'You do not have permission to view this business space.');
        }
        
        $business->load('barangay');
        return view('owner.business.show', compact('business'));
    }
    
    public function edit($id)
    {
        $ownerId = Auth::guard('owner')->id();
        
        // Find business by ID
        $business = Business::find($id);
        
        if (!$business) {
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'Business space not found.');
        }
        
        // Verify ownership
        if ($business->owner_id !== $ownerId) {
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'You do not have permission to edit this business space.');
        }
        
        $owner = Owner::find($ownerId);
        $registeredPermitNumber = $owner->permit_number ?? '';
        
        $barangays = Barangay::orderBy('name')->get();
        $types = ['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'];
        $amenitiesList = ['Parking', '24/7 Security', 'CCTV', 'Backup Power', 'Elevator', 'Air Conditioning', 'Wifi', 'Conference Room', 'Kitchenette', 'Private Restroom'];
        $businessFeatures = ['Loading Bay', 'Delivery Access', 'Signage Space', 'Street Frontage', 'Corner Lot', 'Drive-thru Capable'];
        
        return view('owner.business.edit', compact('business', 'barangays', 'types', 'amenitiesList', 'businessFeatures', 'registeredPermitNumber'));
    }
    
    public function update(Request $request, $id)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            // Find business by ID
            $business = Business::find($id);
            
            if (!$business) {
                return redirect()->route('owner.business-spaces.index')
                    ->with('error', 'Business space not found.');
            }
            
            // Verify ownership
            if ($business->owner_id !== $ownerId) {
                return redirect()->route('owner.business-spaces.index')
                    ->with('error', 'You do not have permission to update this business space.');
            }
            
            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'unit_number' => 'nullable|string|max:50',
                'type' => ['required', Rule::in(['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'])],
                'monthly_rent' => 'required|numeric|min:0',
                'barangay_name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'floor_area_sqm' => 'nullable|integer|min:0',
                'amenities' => 'nullable|array',
                'business_features' => 'nullable|array',
                'description' => 'nullable|string',
                'status' => ['required', Rule::in(['Available', 'Occupied', 'Maintenance', 'Reserved'])],
                'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'removed_images' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
            
            DB::beginTransaction();
            
            // Get barangay ID from name
            $barangay = Barangay::where('name', $validated['barangay_name'])->first();
            
            // Handle images
            $currentImages = $business->images ?? [];
            
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
                    $path = $image->store('business_spaces', 'public');
                    $currentImages[] = $path;
                }
            }
            
            $business->update([
                'business_name' => $validated['business_name'],
                'unit_number' => $validated['unit_number'] ?? null,
                'type' => $validated['type'],
                'price' => $validated['monthly_rent'],
                'monthly_rent' => $validated['monthly_rent'],
                'floor_area_sqm' => $validated['floor_area_sqm'] ?? null,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'],
                'barangay_id' => $barangay->id ?? null,
                'barangay_name' => $validated['barangay_name'],
                'status' => $validated['status'],
                'images' => array_values($currentImages),
                'amenities' => $validated['amenities'] ?? [],
                'business_features' => $validated['business_features'] ?? [],
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);
            
            DB::commit();
            
            return redirect()->route('owner.business-spaces.index')
                ->with('success', 'Business space updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update business error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update business space. Error: ' . $e->getMessage());
        }
    }
    
    public function destroy($id)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            
            // Find business by ID
            $business = Business::find($id);
            
            if (!$business) {
                return redirect()->route('owner.business-spaces.index')
                    ->with('error', 'Business space not found.');
            }
            
            // Verify ownership
            if ($business->owner_id !== $ownerId) {
                return redirect()->route('owner.business-spaces.index')
                    ->with('error', 'You do not have permission to delete this business space.');
            }
            
            DB::beginTransaction();
            
            // Delete images
            $images = $business->images;
            if (is_array($images)) {
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            
            $business->delete();
            
            DB::commit();
            
            return redirect()->route('owner.business-spaces.index')
                ->with('success', 'Business space deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delete business error: ' . $e->getMessage());
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'Failed to delete business space. Error: ' . $e->getMessage());
        }
    }
    
    public function revise($id)
    {
        $ownerId = Auth::guard('owner')->id();
        $business = Business::where('owner_id', $ownerId)
            ->where('verification_status', 'rejected')
            ->firstOrFail();
        
        $barangays = Barangay::orderBy('name')->get();
        $types = ['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'];
        $amenitiesList = ['Parking', '24/7 Security', 'CCTV', 'Backup Power', 'Elevator', 'Air Conditioning', 'Wifi', 'Conference Room', 'Kitchenette', 'Private Restroom'];
        $businessFeatures = ['Loading Bay', 'Delivery Access', 'Signage Space', 'Street Frontage', 'Corner Lot', 'Drive-thru Capable'];
        
        return view('owner.business.revise', compact('business', 'barangays', 'types', 'amenitiesList', 'businessFeatures'));
    }
    
    public function resubmit(Request $request, $id)
    {
        try {
            $ownerId = Auth::guard('owner')->id();
            $business = Business::where('owner_id', $ownerId)
                ->where('verification_status', 'rejected')
                ->firstOrFail();
            
            $validated = $request->validate([
                'business_name' => 'required|string|max:255',
                'unit_number' => 'nullable|string|max:50',
                'type' => ['required', Rule::in(['Office', 'Retail', 'Restaurant', 'Warehouse', 'Co-working', 'Studio', 'Other'])],
                'monthly_rent' => 'required|numeric|min:0',
                'barangay_name' => 'required|string|max:255',
                'address' => 'required|string|max:500',
                'floor_area_sqm' => 'nullable|integer|min:0',
                'amenities' => 'nullable|array',
                'business_features' => 'nullable|array',
                'description' => 'nullable|string',
                'status' => ['required', Rule::in(['Available', 'Occupied', 'Maintenance', 'Reserved'])],
                'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
            ]);
            
            DB::beginTransaction();
            
            // Get barangay ID from name
            $barangay = Barangay::where('name', $validated['barangay_name'])->first();
            
            // Handle images - keep existing and add new ones
            $currentImages = $business->images ?? [];
            
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('business_spaces', 'public');
                    $currentImages[] = $path;
                }
            }
            
            $business->update([
                'business_name' => $validated['business_name'],
                'unit_number' => $validated['unit_number'] ?? null,
                'type' => $validated['type'],
                'price' => $validated['monthly_rent'],
                'monthly_rent' => $validated['monthly_rent'],
                'floor_area_sqm' => $validated['floor_area_sqm'] ?? null,
                'description' => $validated['description'] ?? null,
                'address' => $validated['address'],
                'barangay_id' => $barangay->id ?? null,
                'barangay_name' => $validated['barangay_name'],
                'status' => $validated['status'],
                'images' => array_values($currentImages),
                'amenities' => $validated['amenities'] ?? [],
                'business_features' => $validated['business_features'] ?? [],
                'verification_status' => 'pending',
                'rejection_reason' => null,
                'verified_at' => null,
                'verified_by' => null,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
            ]);
            
            DB::commit();
            
            return redirect()->route('owner.business-spaces.index')
                ->with('success', 'Business space resubmitted for admin verification!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Resubmit business error: ' . $e->getMessage());
            return redirect()->route('owner.business-spaces.index')
                ->with('error', 'Failed to resubmit business space. Error: ' . $e->getMessage());
        }
    }
}