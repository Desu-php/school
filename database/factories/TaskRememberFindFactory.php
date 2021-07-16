<?php

namespace Database\Factories;

use App\Models\TaskRememberFind;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskRememberFindFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskRememberFind::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'prompt' => $this->faker->text(30),
            "task_id" => $this->faker->numberBetween(1100, 1200),
        ];
    }
}
