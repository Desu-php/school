<?php

namespace Database\Seeders;

use App\Models\DynamicPageText;
use Illuminate\Database\Seeder;

class DynamicPageTextSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DynamicPageText::insert([
            [
                'description' => '{"ru": "О нас О нас О нас О нас О нас О нас О нас О нас О нас О нас ", "en": "About us About us About us About us About us About us About us About us"}',
                'is_current' => true,
                'dynamic_page_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => '{"ru": "Политика конфиденциальности Политика конфиденциальности Политика конфиденциальности Политика конфиденциальности ", "en": "Privacy Policy Privacy Policy Privacy Policy Privacy Policy "}',
                'is_current' => true,
                'dynamic_page_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => '{"ru": "Пользовательское соглашение Пользовательское соглашение Пользовательское соглашение Пользовательское соглашение ", "en": "Terms of use Terms of use Terms of use Terms of use "}',
                'is_current' => true,
                'dynamic_page_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'description' => '{"ru": "Правовая оговорка Правовая оговорка Правовая оговорка ", "en": "Legal disclaimer Legal disclaimer Legal disclaimer Legal disclaimer "}',
                'is_current' => true,
                'dynamic_page_id' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
