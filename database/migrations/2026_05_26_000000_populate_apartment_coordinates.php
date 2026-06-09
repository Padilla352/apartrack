<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    public function up(): void
    {
        $apiKey = config('services.google_maps.key');
        
        if (!$apiKey) {
            Log::warning('Google Maps API key not set. Using barangay defaults.');
            $this->useBarangayDefaults();
            return;
        }

        // Get all apartments with NULL coordinates
        $apartments = DB::table('apartments')
            ->whereNull('latitude')
            ->whereNull('longitude')
            ->get(['id', 'address', 'barangay_name']);

        foreach ($apartments as $apartment) {
            // Build full address for geocoding
            $fullAddress = $apartment->address . ', ' . $apartment->barangay_name . ', Binalonan, Pangasinan, Philippines';
            
            try {
                // Call Google Geocoding API
                $response = Http::timeout(10)->get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $fullAddress,
                    'key' => $apiKey,
                ]);

                if ($response->successful() && $response->json('status') === 'OK') {
                    $location = $response->json('results.0.geometry.location');
                    
                    if ($location) {
                        DB::table('apartments')
                            ->where('id', $apartment->id)
                            ->update([
                                'latitude' => $location['lat'],
                                'longitude' => $location['lng']
                            ]);
                        
                        Log::info("Geocoded apartment {$apartment->id}: {$location['lat']}, {$location['lng']}");
                    }
                } else {
                    Log::warning("Geocoding failed for apartment {$apartment->id}: " . $response->json('status'));
                }
            } catch (\Exception $e) {
                Log::error("Geocoding error for apartment {$apartment->id}: " . $e->getMessage());
            }
            
            // Rate limiting - Google API allows ~10 requests per second
            usleep(100000);
        }
    }

    protected function useBarangayDefaults()
    {
        $barangayCoordinates = [
            'Balangobong' => ['latitude' => 16.0456, 'longitude' => 120.3312],
            'Bued' => ['latitude' => 16.0512, 'longitude' => 120.3298],
            'Poblacion' => ['latitude' => 16.0489, 'longitude' => 120.3364],
            'Santa Cruz' => ['latitude' => 16.0545, 'longitude' => 120.3401],
            'San Fernando' => ['latitude' => 16.0420, 'longitude' => 120.3250],
            'Paitan' => ['latitude' => 16.0480, 'longitude' => 120.3280],
            'Palasahan' => ['latitude' => 16.0500, 'longitude' => 120.3350],
        ];

        foreach ($barangayCoordinates as $barangay => $coords) {
            DB::table('apartments')
                ->where('barangay_name', $barangay)
                ->whereNull('latitude')
                ->whereNull('longitude')
                ->update([
                    'latitude' => $coords['latitude'],
                    'longitude' => $coords['longitude']
                ]);
        }
    }

    public function down(): void
    {
        DB::table('apartments')
            ->update([
                'latitude' => null,
                'longitude' => null
            ]);
    }
};

