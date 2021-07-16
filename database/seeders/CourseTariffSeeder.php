<?php

namespace Database\Seeders;

use App\Models\CourseTariff;
use Illuminate\Database\Seeder;

class CourseTariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CourseTariff::insert([
            [
                'name' => '{"ru": "Базовый", "en": "Basic"}',
                'price' => 121.00,
                'duration' => 90,
                'access_extend' => '{"price": 2000, "duration": 180}',
                'automatic_check_tasks' =>  true,
                'freezing_possibility' => null,
                'access_independent_work' => false,
                'access_additional_materials' => false,
                'additional_course_gift' => false,
                'access_dictionary' => true,
                'access_notes' => true,
                'access_grammar' => false,
                'access_chat' => false,
                'feedback_experts' => false,
                'access_fb_chat' => false,
                'access_upgrade_tariff' => null,
                'access_materials_after_purchasing_course' => null,
                'discount_for_family' =>  null,
                'consultation' => null
            ],
            [
                'name' => '{"ru": "Серебряный", "en": "Silver"}',
                'price' => 132.00,
                'duration' => 120,
                'access_extend' => '{"price": 2000, "duration": 180}',
                'automatic_check_tasks' =>  true,
                'freezing_possibility' => null,
                'access_independent_work' => false,
                'access_additional_materials' => false,
                'additional_course_gift' => false,
                'access_dictionary' => true,
                'access_notes' => true,
                'access_grammar' => false,
                'access_chat' => false,
                'feedback_experts' => false,
                'access_fb_chat' => false,
                'access_upgrade_tariff' => null,
                'access_materials_after_purchasing_course' => null,
                'discount_for_family' =>  null,
                'consultation' => null
            ],
            [
                'name' => '{"ru": "Золотой", "en": "Gold"}',
                'price' => 199.00,
                'duration' => 120,
                'access_extend' => '{"price": 2000, "duration": 180}',
                'automatic_check_tasks' =>  true,
                'freezing_possibility' => null,
                'access_independent_work' => false,
                'access_additional_materials' => false,
                'additional_course_gift' => true,
                'access_dictionary' => true,
                'access_notes' => true,
                'access_grammar' => false,
                'access_chat' => false,
                'feedback_experts' => false,
                'access_fb_chat' => false,
                'access_upgrade_tariff' => null,
                'access_materials_after_purchasing_course' => null,
                'discount_for_family' =>  null,
                'consultation' => null
            ]
        ]);
    }
}
