<?php

namespace Database\Factories;

use App\Models\TaskCategorize;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskCategorizeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskCategorize::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'prompt' => $this->faker->text(30),
            "task_id" => $this->faker->unique()->numberBetween(300, 400),
        ];
    }
}
