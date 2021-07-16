<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    protected $sum = 1;
    protected $task_type_id = 1;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $this->sum += 1;
        if ($this->sum < 100){
            $this->task_type_id = 1;
        }else if ($this->sum < 200) {
            $this->task_type_id = 2;
        } else if ($this->sum < 300) {
            $this->task_type_id = 3;
        } else if ($this->sum < 400) {
            $this->task_type_id = 4;
        } else if ($this->sum < 500) {
            $this->task_type_id = 5;
        } else if ($this->sum < 600) {
            $this->task_type_id = 6;
        } else if ($this->sum < 700) {
            $this->task_type_id = 7;
        } else if ($this->sum < 800) {
            $this->task_type_id = 8;
        } else if ($this->sum < 900) {
            $this->task_type_id = 9;
        } else if ($this->sum < 1000) {
            $this->task_type_id = 10;
        } else if ($this->sum < 1100) {
            $this->task_type_id = 11;
        } else if ($this->sum < 1200) {
            $this->task_type_id = 12;
        } else if ($this->sum < 1300) {
            $this->task_type_id = 13;
        }

        return [
            'name' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'description' => ["ru" => $this->faker->text(300), "en" => $this->faker->text(300) ],
            "lesson_block_id" => $this->faker->numberBetween(1, 500),
            "task_type_id" => $this->task_type_id,
            'status_task' => 'inactive',
        ];
    }
}
