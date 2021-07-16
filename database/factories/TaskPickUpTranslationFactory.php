<?php

namespace Database\Factories;

use App\Models\TaskPickUpTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskPickUpTranslationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskPickUpTranslation::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'prompt' => $this->faker->text(30),
            "task_id" => $this->faker->numberBetween(200, 300),
        ];
    }
}
