<?php

namespace Database\Seeders;

use App\Models\ProjectSetting;
use Illuminate\Database\Seeder;

class ProjectSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ProjectSetting::insert([
            [
                'key' => 'phone',
                'value' => json_encode(['+7 000 555 55 55', '+7 000 555 55 55']),
                'field' => 'json',
                'is_default' => true
            ],
            [
                'key' => 'email',
                'value' => json_encode(['info@languagetogo.com', 'suport@languagetogo.com']),
                'field' => 'json',
                'is_default' => true
            ],
            [
                'key' => 'address',
                'value' => '000000, Россия г. Название города,ул. Название, ХХХ, оф. ХХ',
                'field' => 'input',
                'is_default' => true
            ],
            [
                'key' => 'news_slider_count',
                'value' => '10',
                'field' => 'input',
                'is_default' => true
            ],
            [
                'key' => 'interesting_slider_count',
                'value' => '10',
                'field' => 'input',
                'is_default' => true
            ],
            [
                'key' => 'coordinates',
                'value' => '<iframe src="https://yandex.ru/map-widget/v1/?um=constructor%3A7b9c7909cf828e76cc1cd504161295fb5d4e7582d7be83ce335c6094b85fecf4&amp;source=constructor" width="100%" height="400" frameborder="0"></iframe>',
                'field' => 'map',
                'is_default' => true
            ],
            [
                'key' => 'promo_video',
                'value' => json_encode([
                    'video_iframe' => '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/S_dfq9rFWAE" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
                ]),
                'field' => 'json',
                'is_default' => true
            ],
        ]);
    }
}
