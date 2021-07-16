<?php

namespace Database\Factories;

use App\Models\TaskRememberFindWord;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskRememberFindWordFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskRememberFindWord::class;
    protected $sum = 1;
    protected $remember_find_id = 1;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if ($this->sum < 3){
            $this->sum ++;
        } else {
            $this->remember_find_id += 1;
            $this->sum = 0;
        }
        return [
            'word' => $this->faker->word(),
            "remember_find_id" => $this->remember_find_id,
        ];
    }
}
