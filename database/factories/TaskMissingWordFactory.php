<?php

namespace Database\Factories;

use App\Models\TaskMissingWord;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskMissingWordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskMissingWord::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'prompt' => $this->faker->text(30),
            "task_id" => $this->faker->unique()->numberBetween(100, 200),
            'missing_words_text' => $this->faker->word() .(' [ '. $this->faker->word .' ] '). $this->faker->text(20) .(' [ '. $this->faker->word .' ] ').$this->faker->text(20)
        ];
    }
}
