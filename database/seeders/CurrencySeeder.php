<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Currency::insert([
            [
                'name' => '{"ru": "Руб"}',
                'code' => 'rub',
                'symbol' => '₽',
                'is_main' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Доллар", "en": "Usd"}',
                'code' => 'usd',
                'symbol' => '$',
                'is_main' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Евро", "en": "Eur"}',
                'code' => 'eur',
                'symbol' => '€',
                'is_main' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        Artisan::call("check:exchange-rate");
    }
}
