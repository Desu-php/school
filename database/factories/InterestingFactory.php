<?php

namespace Database\Factories;

use App\Models\Interesting;
use Illuminate\Database\Eloquent\Factories\Factory;

class InterestingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Interesting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text(50),
            'description' => $this->faker->text(800),
            'short_description' => $this->faker->text(500),
            'category_interesting_id' => $this->faker->numberBetween(1, 3),
            'video_iframe' => '<iframe src="https://www.youtube.com/embed/OmWHlGKa-E8" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>'
        ];
    }
}
