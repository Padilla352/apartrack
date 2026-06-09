<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage; // <-- Add this for image URLs

class ApartmentController extends Controller
{
    /**
     * Display apartment details.
     *
     * @param  string  $barangayId  (slug, not used but kept for route)
     * @param  int     $apartmentId
     * @return \Illuminate\View\View
     */
    public function show($barangayId, $apartmentId)
    {
        // Load apartment with owner details
        $apartment = DB::table('apartments')
            ->leftJoin('owners', 'apartments.owner_id', '=', 'owners.id')
            ->select(
                'apartments.id',
                'apartments.name',
                'apartments.unit_number',
                'apartments.type',
                'apartments.bedrooms',
                'apartments.bathrooms',
                'apartments.monthly_rent',
                'apartments.floor_area_sqm',
                'apartments.description',
                'apartments.address',
                'apartments.barangay_name',
                'apartments.permit_number',
                'apartments.status',
                'apartments.images',
                'apartments.verification_status',
                'apartments.latitude',
                'apartments.longitude',
                'owners.id as owner_id',
                'owners.name as owner_name',
                'owners.email as owner_email',
                'owners.phone as owner_phone',
                'owners.facebook_username'
            )
            ->where('apartments.id', $apartmentId)
            ->where('apartments.verification_status', 'approved')
            ->first();

        if (!$apartment) {
            abort(404, 'Apartment not found or pending approval.');
        }

        // Decode images (JSON string -> array)
        $images = [];
        if (!empty($apartment->images)) {
            if (is_array($apartment->images)) {
                $images = $apartment->images;
            } elseif (is_string($apartment->images)) {
                $decoded = json_decode($apartment->images, true);
                if (is_array($decoded)) {
                    $images = array_values(array_filter($decoded));
                } elseif (!empty($apartment->images)) {
                    $images = [$apartment->images];
                }
            }
        }

        // Convert each image path to a full URL using Storage
        $imageUrls = array_map(function ($img) {
            if (empty($img)) return null;
            // If it's already an absolute URL, return as is
            if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
            // Otherwise, generate URL via Storage disk
            return Storage::url(ltrim($img, '/'));
        }, $images);
        $imageUrls = array_values(array_filter($imageUrls));

        // Build the array expected by the view
        $apartmentArray = [
            'id'            => $apartment->id,
            'name'          => $apartment->name,
            'unit_number'   => $apartment->unit_number,
            'type'          => $apartment->type,
            'bedrooms'      => $apartment->bedrooms,
            'bathrooms'     => $apartment->bathrooms,
            'monthly_rent'  => $apartment->monthly_rent,
            'floor_area_sqm'=> $apartment->floor_area_sqm,
            'description'   => $apartment->description,
            'address'       => $apartment->address,
            'barangay_name' => $apartment->barangay_name,
            'permit_number' => $apartment->permit_number,
            'status'        => $apartment->status,
            'verification_status' => $apartment->verification_status,
            'latitude'      => $apartment->latitude,
            'longitude'     => $apartment->longitude,
            'owner_id'      => $apartment->owner_id,
            'owner_name'    => $apartment->owner_name,
            'owner_email'   => $apartment->owner_email,
            'owner_phone'   => $apartment->owner_phone,
            'facebook_username' => $apartment->facebook_username,
            // Original images array (relative paths)
            'images'        => $images,
            // Full URLs for display
            'image_urls'    => $imageUrls,
            // Default Facebook page link (fallback)
            'facebook_url'  => $apartment->facebook_username 
                ? 'https://facebook.com/' . ltrim($apartment->facebook_username, '@')
                : 'https://www.facebook.com/kharl.luxx',
            'rating'        => 0,      // Add rating logic if you have a reviews table
            'review_count'  => 0,
        ];

        // Get Google Maps API key from config (safer than env() directly)
        $apiKey = config('services.google_maps.key');

        return view('user.apartment-details', [
            'apartment' => $apartmentArray,
            'apiKey'    => $apiKey,
        ]);
    }
}