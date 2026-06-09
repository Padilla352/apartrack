<?php

namespace App\Helpers;

class ApartmentHelper
{
    /**
     * Generate mock apartments for a given barangay.
     * Returns an array keyed by apartment ID (1..$totalCount).
     *
     * @param string $barangayId
     * @param int $availableCount
     * @param int $totalCount
     * @return array
     */
    public static function generateApartments($barangayId, $availableCount, $totalCount)
    {
        $apartments = [];
        $unitTypes = ['Studio', '1BR', '2BR', '3BR'];
        $sizes = [25, 30, 35, 40, 45, 50, 55, 60];
        $prices = [5000, 6000, 7000, 8000, 9000, 10000, 12000, 15000];
        $apartmentNames = [
            'Poblacion Garden 1', 'Poblacion Garden 2', 'Greenfield Tower',
            'WCC Prime Residences', 'Town Proper Suites', 'Metro View Heights'
        ];
        $sampleImages = [
            "https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=400&h=300&fit=crop",
            "https://images.unsplash.com/photo-1501183007986-d0d080b147f9?w=400&h=300&fit=crop",
            "https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=400&h=300&fit=crop",
            "https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=400&h=300&fit=crop"
        ];

        $coordinates = [
            'Balangobong' => ['lat' => 16.0489, 'lng' => 120.3364, 'name' => 'Balangobong'],
            'default' => ['lat' => 16.0489, 'lng' => 120.3364, 'name' => 'Binalonan']
        ];
        $coord = $coordinates[$barangayId] ?? $coordinates['default'];

        for ($i = 1; $i <= $totalCount; $i++) {
            $isAvailable = $i <= $availableCount;
            $price = $prices[$i % count($prices)];
            $rating = 3 + ($i % 3);

            // Use apartment ID as the array key
            $apartments[$i] = [
                'id' => $i,
                'ownerId' => $i,
                'name' => $apartmentNames[$i % count($apartmentNames)],
                'unitName' => "Unit " . chr(64 + ceil($i / 6)) . "{$i} - " . $unitTypes[$i % count($unitTypes)],
                'size' => $sizes[$i % count($sizes)] . " sqm",
                'price' => "₱" . number_format($price) . " / month",
                'status' => $isAvailable ? 'available' : 'occupied',
                'features' => 'Studio type, bathroom, cabinet, window type air con, shared kitchen, CCTV',
                'ownerName' => 'Juan Dela Cruz',
                'ownerAge' => 45,
                'ownerEmail' => 'juandelacruz@gmail.com',
                'ownerDateStarted' => 'March 10, 2026',
                'ownerPermitEnds' => 'March 10, 2028',
                'ownerPhone' => '09123456789',
                'location' => "{$coord['name']}, Binalonan, Pangasinan",
                'apartmentType' => $unitTypes[$i % count($unitTypes)],
                'coordinates' => $coord,
                'images' => $sampleImages,
                'rating' => $rating,
                'reviewCount' => rand(10, 60)
            ];
        }

        return $apartments;
    }
}