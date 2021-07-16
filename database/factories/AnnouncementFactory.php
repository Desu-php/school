<?php

namespace Database\Factories;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Announcement::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'description' =>  ["ru" => $this->faker->text(300), "en" => $this->faker->text(300) ],
            'teaching_language_id' => $this->faker->numberBetween(1, 2),
            'video_iframe' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/qOGhHpLQtHQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        ];
    }
}
