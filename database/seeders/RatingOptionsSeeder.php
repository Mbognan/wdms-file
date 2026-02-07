<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\RatingOptions;

class RatingOptionsSeeder extends Seeder
{
    public function run(): void
    {
        $options = [
            [
                'label' => 'Available',
                'min_score' => 3,
                'max_score' => 5,
                'applicable' => true,
            ],
            [
                'label' => 'Available but Inadequate',
                'min_score' => 1,
                'max_score' => 2,
                'applicable' => true,
            ],
            [
                'label' => 'Not Available',
                'min_score' => 0,
                'max_score' => 0,
                'applicable' => true,
            ],
            [
                'label' => 'Not Applicable',
                'min_score' => null,
                'max_score' => null,
                'applicable' => false,
            ],
        ];

        foreach ($options as $option) {
            RatingOptions::updateOrCreate(
                ['label' => $option['label']], // unique key
                $option
            );
        }
    }
}

