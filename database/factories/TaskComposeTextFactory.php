<?php

namespace Database\Factories;

use App\Models\TaskComposeText;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskComposeTextFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskComposeText::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'prompt' => $this->faker->text(30),
            "task_id" => $this->faker->unique()->numberBetween(400, 501),
            "missing_words_text" => 'Define the [models] default [state].',
        ];
    }
}
