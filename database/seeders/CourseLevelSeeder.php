<?php

namespace Database\Seeders;

use App\Models\CourseLevel;
use App\Models\CourseTariff;
use Illuminate\Database\Seeder;

class CourseLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CourseLevel::insert([
            [
                'name' => '{"ru": "Начальный", "en": "Initial"}',
            ],
            [
                'name' => '{"ru": "Средний", "en": "Average"}',
            ],
            [
                'name' => '{"ru": "Сложный", "en": "Difficult"}',
            ]
        ]);
    }
}
