<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Apartment;
use App\Models\Barangay;
use App\Models\BusinessSpace;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ExploreController extends Controller
{
    /**
     * Get explore data for the Flutter app.
     * Returns popular boarding houses, commercial spaces, barangay apartments, and barangays.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Popular Boarding Houses (type = 'boarding')
            $boardingProperties = $this->getBoardingProperties();

            // Commercial Spaces
            $commercialSpaces = $this->getCommercialSpaces();

            // Barangay Apartments (apartments for each barangay)
            $barangayApartments = $this->getBarangayApartments();

            // Barangays with logos
            $barangays = $this->getBarangays();

            return response()->json([
                'data' => [
                    'popular_boarding' => $boardingProperties,
                    'commercial_spaces' => $commercialSpaces,
                    'barangay_apartments' => $barangayApartments,
                    'barangays' => $barangays,
                ]
            ]);
        } catch (\Exception $e) {
            // Return mock data as fallback if database query fails
            return $this->getMockData();
        }
    }

    /**
     * Get approved boarding house properties.
     */
    private function getBoardingProperties(): array
    {
        $boarding = Apartment::where('type', 'boarding')
            ->where('verification_status', 'approved')
            ->where('status', 'available')
            ->select(
                'id', 'name as title', 'monthly_rent as price',
                'address', 'images', 'bedrooms', 'description'
            )
            ->limit(10)
            ->get();

        return $boarding->map(function ($item) {
            $images = $this->processImages($item->images);
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => (float) $item->price,
                'rating' => 4,
                'imageUrl' => $images[0] ?? 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=800&h=600&fit=crop',
                'address' => $item->address ?? '',
                'features' => $item->description ?? ($item->bedrooms ? $item->bedrooms . ' bed(s)' : 'Boarding house'),
            ];
        })->toArray();
    }

    /**
     * Get approved commercial spaces.
     */
    private function getCommercialSpaces(): array
    {
        $commercial = BusinessSpace::where('verification_status', 'approved')
            ->where('status', 'available')
            ->select(
                'id', 'name as title', 'monthly_rent as price',
                'address', 'images', 'space_type', 'description'
            )
            ->limit(10)
            ->get();

        return $commercial->map(function ($item) {
            $images = $this->processImages($item->images);
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => (float) $item->price,
                'rating' => 4,
                'imageUrl' => $images[0] ?? 'https://images.unsplash.com/photo-1494526585095-c41746248156?w=800&h=600&fit=crop',
                'address' => $item->address ?? '',
                'features' => $item->description ?? ($item->space_type ?? 'Commercial space'),
            ];
        })->toArray();
    }

    /**
     * Get apartments for barangays.
     */
    private function getBarangayApartments(): array
    {
        $apartments = Apartment::where('type', 'apartment')
            ->where('verification_status', 'approved')
            ->where('status', 'available')
            ->select(
                'id', 'name as title', 'monthly_rent as price',
                'address', 'images', 'bedrooms', 'description'
            )
            ->limit(10)
            ->get();

        return $apartments->map(function ($item) {
            $images = $this->processImages($item->images);
            return [
                'id' => $item->id,
                'title' => $item->title,
                'price' => (float) $item->price,
                'rating' => 4,
                'imageUrl' => $images[0] ?? 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800&h=600&fit=crop',
                'address' => $item->address ?? '',
                'features' => $item->description ?? ($item->bedrooms ? $item->bedrooms . ' bedroom(s)' : 'Apartment'),
            ];
        })->toArray();
    }

    /**
     * Get all barangays with their logos.
     */
    private function getBarangays(): array
    {
        $barangays = Barangay::select('id', 'name', 'logo_url', 'latitude', 'longitude')
            ->get();

        $apartmentCount = Apartment::where('verification_status', 'approved')
            ->select('barangay_name', DB::raw('COUNT(*) as count'))
            ->groupBy('barangay_name')
            ->pluck('count', 'barangay_name')
            ->toArray();

        return $barangays->map(function ($item) use ($apartmentCount) {
            $name = $item->name;
            $available = $apartmentCount[$name] ?? 0;
            $logoUrl = $item->logo_url;

            if ($logoUrl && !filter_var($logoUrl, FILTER_VALIDATE_URL)) {
                $logoUrl = Storage::url($logoUrl);
            }

            return [
                'id' => $item->id,
                'name' => $name,
                'logo_url' => $logoUrl ?? '',
                'available' => $available,
                'total' => $available * 2,
                'latitude' => $item->latitude,
                'longitude' => $item->longitude,
            ];
        })->toArray();
    }

    /**
     * Process images JSON/array into array of URLs.
     */
    private function processImages($images): array
    {
        $imageList = [];

        if (empty($images)) {
            return $imageList;
        }

        if (is_string($images)) {
            $decoded = json_decode($images, true);
            if (is_array($decoded)) {
                $imageList = $decoded;
            } else {
                $imageList = [$images];
            }
        } elseif (is_array($images)) {
            $imageList = $images;
        }

        return array_values(array_filter(array_map(function ($img) {
            if (empty($img)) return null;
            if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
            return Storage::url(ltrim($img, '/'));
        }, $imageList)));
    }

    /**
     * Return mock data when database is unavailable.
     */
    private function getMockData(): JsonResponse
    {
        $barangayNames = [
            'Balangobong', 'Bued', 'Bugayong', 'Camangaan', 'Canarvacanan',
            'Capas', 'Cili', 'Dumayat', 'Linmansangan', 'Mangcasuy',
            'Moreno', 'Pasileng Norte', 'Pasileng Sur', 'Poblacion',
            'San Felipe Central', 'San Felipe Sur', 'San Pablo', 'Santiago',
            'Santonino', 'Sta. Catalina', 'Sta. Maria Norte', 'Sumabnit',
            'Tabuyoc', 'Vacante'
        ];

        $popularBoarding = [
            ['id' => 1, 'title' => 'Zhaygel Boarding House', 'price' => 2500.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=800&h=600&fit=crop',
                'address' => 'Zone 3 Cabero Street', 'features' => 'Studio type, shared kitchen, CCTV'],
            ['id' => 2, 'title' => 'Imelda Boarding House', 'price' => 2800.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?w=800&h=600&fit=crop',
                'address' => 'Cabida Street', 'features' => '1BR, bathroom, parking area'],
            ['id' => 3, 'title' => 'Eduardo Boarding House', 'price' => 2500.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800&h=600&fit=crop',
                'address' => 'Cabero Street', 'features' => 'Affordable dorm type room'],
            ['id' => 4, 'title' => 'Jesca Boarding House', 'price' => 2200.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1493666438817-866a91353ca9?w=800&h=600&fit=crop',
                'address' => 'Carisville Subd', 'features' => 'Budget-friendly room rental'],
        ];

        $commercialSpaces = [
            ['id' => 5, 'title' => 'SJE Boarding House', 'price' => 5000.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=800&h=600&fit=crop',
                'address' => 'Carisville Subd', 'features' => 'Commercial stall, parking available, CCTV'],
            ['id' => 6, 'title' => 'Magsaysay Commercial Space', 'price' => 7500.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?w=800&h=600&fit=crop',
                'address' => 'Magsaysay Street', 'features' => 'Ground floor retail space'],
            ['id' => 7, 'title' => 'Poblacion Market Stall', 'price' => 6000.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800&h=600&fit=crop',
                'address' => 'Poblacion Public Market', 'features' => 'Prime location market stall'],
            ['id' => 8, 'title' => 'Riverside Commercial Space', 'price' => 8500.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1493666438817-866a91353ca9?w=800&h=600&fit=crop',
                'address' => 'Riverside Avenue', 'features' => 'With parking and security'],
        ];

        $barangayApartments = [
            ['id' => 9, 'title' => 'Sunset View Apartments', 'price' => 12000.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?w=800&h=600&fit=crop',
                'address' => 'Barangay Hall Road', 'features' => '2BR, balcony, parking'],
            ['id' => 10, 'title' => 'Greenfield Residences', 'price' => 15000.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1494526585095-c41746248156?w=800&h=600&fit=crop',
                'address' => 'Greenfield Subdivision', 'features' => 'Modern design, swimming pool'],
            ['id' => 11, 'title' => 'City Center Apartments', 'price' => 18000.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=800&h=600&fit=crop',
                'address' => 'City Center', 'features' => 'Close to schools and market'],
            ['id' => 12, 'title' => 'Garden Oasis', 'price' => 10000.0, 'rating' => 4,
                'imageUrl' => 'https://images.unsplash.com/photo-1493666438817-866a91353ca9?w=800&h=600&fit=crop',
                'address' => 'Garden Street', 'features' => 'Quiet neighborhood, garden view'],
        ];

        $barangays = collect($barangayNames)->map(function ($name, $index) {
            return [
                'id' => $index + 1,
                'name' => $name,
                'logo_url' => "assets/images/brgy_logo/" . strtolower(str_replace([' ', '.'], ['_', ''], $name)) . ".png",
                'available' => 12,
                'total' => 24,
                'latitude' => null,
                'longitude' => null,
            ];
        })->toArray();

        return response()->json([
            'data' => [
                'popular_boarding' => $popularBoarding,
                'commercial_spaces' => $commercialSpaces,
                'barangay_apartments' => $barangayApartments,
                'barangays' => $barangays,
            ]
        ]);
    }
}
