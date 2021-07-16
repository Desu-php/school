<?php

namespace Database\Factories;

use App\Models\TeachingVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeachingVideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeachingVideo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(40),
            'description' => $this->faker->text(800),
            'image' => 'faker_video',
            'image_extension' => 'jpg',
            'image_alt' => 'image',
            'video_iframe' => '<iframe src="https://player.vimeo.com/video/27790825?autoplay=1&loop=1&color=ffffff&title=0&portrait=0" width="640" height="480" frameborder="0" allow="fullscreen; picture-in-picture" allowfullscreen></iframe>',
        ];
    }
}
