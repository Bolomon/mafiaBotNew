<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\LevelScope;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            ['level' => 1, 'min_scope' => 0, 'max_scope' => 99],
            ['level' => 2, 'min_scope' => 100, 'max_scope' => 249],
            ['level' => 3, 'min_scope' => 250, 'max_scope' => 449],
            ['level' => 4, 'min_scope' => 450, 'max_scope' => 699],
            ['level' => 5, 'min_scope' => 700, 'max_scope' => 949],
            ['level' => 6, 'min_scope' => 950, 'max_scope' => 1199],
            ['level' => 7, 'min_scope' => 1200, 'max_scope' => 1449],
            ['level' => 8, 'min_scope' => 1450, 'max_scope' => 1699],
            ['level' => 9, 'min_scope' => 1700, 'max_scope' => 1949],
            ['level' => 10, 'min_scope' => 1950, 'max_scope' => 2249],
            ['level' => 11, 'min_scope' => 2250, 'max_scope' => 2549],
            ['level' => 12, 'min_scope' => 2550, 'max_scope' => 2849],
            ['level' => 13, 'min_scope' => 2850, 'max_scope' => 3149],
            ['level' => 14, 'min_scope' => 3150, 'max_scope' => 3449],
            ['level' => 15, 'min_scope' => 3450, 'max_scope' => 3749],
            ['level' => 16, 'min_scope' => 3750, 'max_scope' => 4049],
            ['level' => 17, 'min_scope' => 4050, 'max_scope' => 4349],
            ['level' => 18, 'min_scope' => 4350, 'max_scope' => 4649],
            ['level' => 19, 'min_scope' => 4650, 'max_scope' => 4949],
            ['level' => 20, 'min_scope' => 4950, 'max_scope' => 5449],
            ['level' => 21, 'min_scope' => 5450, 'max_scope' => 5949],
            ['level' => 22, 'min_scope' => 5950, 'max_scope' => 6449],
            ['level' => 23, 'min_scope' => 6450, 'max_scope' => 6949],
            ['level' => 24, 'min_scope' => 6950, 'max_scope' => 7449],
            ['level' => 25, 'min_scope' => 7450, 'max_scope' => 7949],
            ['level' => 26, 'min_scope' => 7950, 'max_scope' => 8449],
            ['level' => 27, 'min_scope' => 8450, 'max_scope' => 8949],
            ['level' => 28, 'min_scope' => 8950, 'max_scope' => 9449],
            ['level' => 29, 'min_scope' => 9450, 'max_scope' => 9949],
            ['level' => 30, 'min_scope' => 9950, 'max_scope' => 99999],
        ];

        foreach ($levels as $level) {
            LevelScope::create($level);
        }
    }
}
