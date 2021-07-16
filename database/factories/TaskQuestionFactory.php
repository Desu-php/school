<?php

namespace Database\Factories;

use App\Models\TaskQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'prompt' => $this->faker->text(30),
            'question' => $this->faker->text(30),
            "task_id" => $this->faker->numberBetween(1, 100),
        ];
    }
}
