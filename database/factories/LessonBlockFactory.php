<?php

namespace Database\Factories;

use App\Models\LessonBlock;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonBlockFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = LessonBlock::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'description' => ["ru" => $this->faker->text(300), "en" => $this->faker->text(300) ],
            'lesson_id' => $this->faker->numberBetween(1, 500),
        ];
    }
}
