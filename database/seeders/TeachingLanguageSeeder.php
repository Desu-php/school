<?php

namespace Database\Seeders;

use App\Models\TeachingLanguage;
use Illuminate\Database\Seeder;

class TeachingLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TeachingLanguage::insert([
            [
                'name' => '{"ru": "Немецкий язык", "en": "German"}',
                'color' => 'linear-gradient(90deg,#e3342f 0,#ffed4a)',
                "letters" => "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z",
                'flag' => 'de.png',
                'code' => 'de',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '{"ru": "Китайский язык", "en": "Chinese"}',
                'color' => 'linear-gradient(90deg,#2e90d1 0,#78258d)',
                "letters" => "a",
                'flag' => 'zh.png',
                'code' => 'zh',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
