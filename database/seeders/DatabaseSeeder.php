<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Admin; // Import ang Admin model
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Gagawa ito ng account sa 'admins' table
        Admin::create([
            'name' => 'Admin User',
            'email' => 'apartrack123@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

    
    }
}
