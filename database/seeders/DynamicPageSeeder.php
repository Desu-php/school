<?php

namespace Database\Seeders;

use App\Models\DynamicPage;
use Illuminate\Database\Seeder;

class DynamicPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DynamicPage::insert([
            [
                'title' => '{"ru": "О нас", "en": "About us"}',
                'key' => 'about_us',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '{"ru": "Политика конфиденциальности", "en": "Privacy Policy"}',
                'key' => 'privacy_policy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '{"ru": "Пользовательское соглашение", "en": "Terms of use"}',
                'key' => 'terms_of_use',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => '{"ru": "Правовая оговорка", "en": "legal_disclaimer"}',
                'key' => 'legal_disclaimer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
