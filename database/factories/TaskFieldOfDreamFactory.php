<?php

namespace Database\Factories;

use App\Models\TaskFieldOfDream;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFieldOfDreamFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskFieldOfDream::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'word' => $this->faker->word(),
            'prompt' => $this->faker->text(30),
            "task_id" => $this->faker->unique()->numberBetween(700, 800),
            "select_dictionary" => $this->faker->numberBetween(0, 1),
        ];
    }
}
