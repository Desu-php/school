<?php

namespace Database\Factories;

use App\Models\TaskDeckCardAnswer;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskDeckCardAnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskDeckCardAnswer::class;
    protected $sum = 0;
    protected $task_deck_card_question_id = 1;
    protected $correct_answer = 0;
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
            $this->task_deck_card_question_id += 1;
            $this->correct_answer = 1;
            $this->sum = 0;
        }
        return [
            'answer' => $this->faker->word(),
            "task_deck_card_question_id" => $this->task_deck_card_question_id,
            "correct_answer" => $this->correct_answer,
        ];
    }
}
