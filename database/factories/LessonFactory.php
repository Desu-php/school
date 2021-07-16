<?php

namespace Database\Factories;

use App\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'description' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'short_description' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'video_iframe' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/qOGhHpLQtHQ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
            'is_free' => $this->faker->numberBetween(0,1),
            'course_module_id' => $this->faker->numberBetween(1, 200),
        ];
    }
}
