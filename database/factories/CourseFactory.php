<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    protected $course_type  = ['additional', 'main'];
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => ["ru" => $this->faker->text(30), "en" => $this->faker->text(30) ],
            'description' => ["ru" => $this->faker->text(600), "en" => $this->faker->text(600) ],
            'teaching_language_id' => $this->faker->numberBetween(1, 2),
            'course_level_id' => $this->faker->numberBetween(1, 2),
            'announcement_id' => $this->faker->numberBetween(1, 80),
            'is_free' => $this->faker->boolean(0),
            'is_free_lesson' => $this->faker->boolean(0),
            'course_type' => $this->course_type[$this->faker->numberBetween(0, 1)]
        ];
    }
}
