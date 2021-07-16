<?php

namespace Database\Factories;

use App\Models\TaskGallow;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskGallowFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskGallow::class;

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
            "task_id" => $this->faker->unique()->numberBetween(800, 900),
            "select_dictionary" => $this->faker->numberBetween(0, 1),
        ];
    }
}
