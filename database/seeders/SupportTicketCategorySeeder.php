<?php

namespace Database\Seeders;

use App\Models\SupportTicketCategory;
use App\Models\TechnicalSupportTicketCategory;
use Illuminate\Database\Seeder;

class SupportTicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SupportTicketCategory::insert([
            [
                'name' => '{"ru": "Проблемы с оплатой", "en": "Problems with payment"}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Вопросы по курсам", "en": "Questions about courses"}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Ошибка с загрузкой", "en": "Error loading"}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Ошибка в тексте или упражнении", "en": "Error in text or exercise"}',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
