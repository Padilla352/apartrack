<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangaySeeder extends Seeder
{
    public function run()
    {
        $barangays = [
            'Balangobong', 'Bued', 'Bugayong', 'Camangaan', 'Canarvacanan',
            'Capas', 'Cili', 'Dumayat', 'Linmansangan', 'Mangcasuy',
            'Moreno', 'Pasileng Norte', 'Pasileng Sur', 'Poblacion',
            'San Felipe Central', 'San Felipe Sur', 'San Pablo', 'Santiago',
            'Santonino', 'Sta. Catalina', 'Sta. Maria Norte', 'Sumabnit',
            'Tabuyoc', 'Vacante'
        ];

        foreach ($barangays as $name) {
            DB::table('barangays')->updateOrInsert(
                ['name' => $name],
                [
                    'latitude'  => null,
                    'longitude' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('24 barangays seeded successfully!');
    }
}