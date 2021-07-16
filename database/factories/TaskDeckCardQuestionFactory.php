<?php

namespace Database\Factories;

use App\Models\TaskDeckCardQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskDeckCardQuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskDeckCardQuestion::class;
    protected $sum = 0;
    protected $task_id = 900;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if ($this->sum < 9){
            $this->sum ++;
        } else {
            $this->task_id += 1;
            $this->sum = 0;
        }
        return [
            'prompt' => $this->faker->text(30),
            'question' => $this->faker->text(30),
            "task_id" => $this->task_id,
        ];
    }
}
