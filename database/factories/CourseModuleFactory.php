<?php

namespace Database\Factories;

use App\Models\CourseModule;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseModuleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseModule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(30),
            'description' => $this->faker->text(100),
            'course_id' => $this->faker->numberBetween(1, 80),
        ];
    }
}
