<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentMethod::insert([
            [
                'name' => '{"ru": "PayPal", "en": "PayPal"}',
                'description' => '{"ru": "description", "en": "description"}',
                'image' => 'pay-pal.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Оплата картами Mastercard, Visa и Maestro", "en": "Payment by cards Mastercard, Visa и Maestro"}',
                'description' => '{"ru": "Оплата картами", "en": "Payment by cards "}',
                'image' => 'mastercard-visa.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Мир", "en": "Мир"}',
                'description' => '{"ru": "Мир", "en": "Мир"}',
                'image' => 'mir.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Яндекс.Деньги", "en": "Yandex Money"}',
                'description' => '{"ru": "description", "en": "description"}',
                'image' => 'yandex.png',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
