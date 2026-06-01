<?php

namespace Database\Seeders;

use App\Models\BannedWord;
use Illuminate\Database\Seeder;

class BannedWordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = [
            ['word' => 'spam', 'severity' => 'low', 'action' => 'pending'],
            ['word' => 'quảng cáo', 'severity' => 'low', 'action' => 'pending'],
            ['word' => 'scam', 'severity' => 'medium', 'action' => 'hide'],
            ['word' => 'lừa đảo', 'severity' => 'medium', 'action' => 'hide'],
            ['word' => 'toxic', 'severity' => 'low', 'action' => 'pending'],
        ];

        foreach ($words as $data) {
            BannedWord::updateOrCreate(
                ['word' => $data['word']],
                [
                    'severity' => $data['severity'],
                    'action' => $data['action'],
                    'is_active' => true,
                ]
            );
        }
    }
}
