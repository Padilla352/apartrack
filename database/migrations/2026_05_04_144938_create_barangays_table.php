<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('barangays', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Insert the barangay list
        $barangays = [
            'Balangobong',
            'Bued',
            'Bugayong',
            'Camangaan',
            'Canarvacanan',
            'Capas',
            'Cili',
            'Dumayat',
            'Linmansangan',
            'Mangcasuy',
            'Moreno',
            'Pasileng Norte',
            'Pasileng Sur',
            'Poblacion',
            'San Felipe Central',
            'San Felipe Sur',
            'San Pablo',
            'Santa Catalina',
            'Santa Maria Norte',
            'Santiago',
            'Santo Niño',
            'Sumabnit',
            'Tabuyoc',
            'Vacante',
        ];

        foreach ($barangays as $barangay) {
            DB::table('barangays')->insert([
                'name' => $barangay,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangays');
    }
};