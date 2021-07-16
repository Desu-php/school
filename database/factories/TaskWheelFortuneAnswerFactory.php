<?php

namespace Database\Factories;

use App\Models\TaskWheelFortuneAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskWheelFortuneAnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskWheelFortuneAnswer::class;
    protected $sum = 0;
    protected $correct_answer = 0;
    protected $task_wheel_fortune_question_id = 1;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if ($this->sum < 3){
            $this->sum ++;
            $this->correct_answer = 0;
        } else {
            $this->task_wheel_fortune_question_id += 1;
            $this->sum = 0;
            $this->correct_answer = 1;
        }
        return [
            'answer' => $this->faker->word(),
            "task_wheel_fortune_question_id" => $this->task_wheel_fortune_question_id,
            "correct_answer" => $this->correct_answer,
        ];
    }
}
