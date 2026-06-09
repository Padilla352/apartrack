<?php

namespace App\Console\Commands;

use App\Models\Apartment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeocodeExistingApartments extends Command
{
    protected $signature = 'apartments:geocode';
    protected $description = 'Geocode all apartments that have missing latitude/longitude';

    public function handle()
    {
        $apartments = Apartment::whereNull('latitude')->orWhereNull('longitude')->get();
        
        if ($apartments->isEmpty()) {
            $this->info('No apartments need geocoding.');
            return;
        }
        
        $apiKey = config('services.google_maps.key');
        if (!$apiKey) {
            $this->error('Google Maps API key missing in config/services.php');
            return;
        }
        
        $count = 0;
        foreach ($apartments as $apt) {
            $fullAddress = $apt->address . ', ' . $apt->barangay_name . ', Binalonan, Pangasinan, Philippines';
            $this->info("Geocoding: {$apt->name} - {$fullAddress}");
            
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $fullAddress,
                'key' => $apiKey,
            ]);
            
            if ($response->successful() && $response->json('status') === 'OK' && count($response->json('results'))) {
                $location = $response->json('results.0.geometry.location');
                $apt->latitude = $location['lat'];
                $apt->longitude = $location['lng'];
                $apt->save();
                $this->info("✅ Updated: {$apt->name} -> lat: {$location['lat']}, lng: {$location['lng']}");
                $count++;
            } else {
                $this->warn("❌ Failed: {$apt->name}");
                Log::warning("Geocoding failed for apartment ID {$apt->id}: " . $response->json('status'));
            }
            
            // Sleep to avoid hitting rate limits
            usleep(200000); // 0.2 seconds
        }
        
        $this->info("Completed. Geocoded {$count} apartments.");
    }
}