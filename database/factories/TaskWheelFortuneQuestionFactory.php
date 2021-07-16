<?php

namespace Database\Factories;

use App\Models\TaskWheelFortuneQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskWheelFortuneQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskWheelFortuneQuestion::class;
    protected $sum = 0;
    protected $task_id = 1000;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if ($this->sum < 7){
            $this->sum ++;
        } else {
            $this->task_id += 1;
            $this->sum = 0;
        }
        return [
            'prompt' => $this->faker->text(30),
            'question' => $this->faker->word,
            "task_id" => $this->task_id,
        ];
    }
}
