<?php

namespace Database\Factories;

use App\Models\TaskTranslation;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskTranslationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskTranslation::class;
    protected $sum = 0;
    protected $pick_up_translation_id = 1;
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
            $this->pick_up_translation_id += 1;
            $this->sum = 0;
        }
        return [
            'translation' => $this->faker->text(20),
            'phrase' => $this->faker->text(20),
            "pick_up_translation_id" => $this->pick_up_translation_id,
        ];
    }
}
