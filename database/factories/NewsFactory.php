<?php

namespace Database\Factories;

use App\Models\News;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = News::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'short_description' => ["ru" => $this->faker->text(150), "en" => $this->faker->text(150) ],
            'description' => ["ru" => $this->faker->text(600), "en" => $this->faker->text(600) ],
            'image' => 'faker_news',
            'image_extension' => 'jpg',
            'video_iframe' => '<iframe src="https://player.vimeo.com/video/27790825?autoplay=1&loop=1&color=ffffff&title=0&portrait=0" width="640" height="480" frameborder="0" allow="fullscreen; picture-in-picture" allowfullscreen></iframe>',
        ];
    }
}
