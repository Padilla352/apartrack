<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Faq::create([
            'question' => 'How do I search for apartments?',
            'answer' => 'Use the search bar at the top...',
            'category' => 'General',
            'order' => 1,
        ]);

        Faq::create([
            'question' => 'How can I contact an owner?',
            'answer' => 'After logging in, click "Chat Owner" on any listing...',
            'category' => 'General',
            'order' => 2,
        ]);

        Faq::create([
            'question' => 'Is there a mobile app?',
            'answer' => 'We are currently developing a mobile app...',
            'category' => 'Technical',
            'order' => 3,
        ]);
    }
}