<?php

namespace Database\Factories;

use App\Models\CourseChat;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseChatFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CourseChat::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(30),
            'status' => 'accepted',
            'course_id' => $this->faker->numberBetween(1, 80),
        ];
    }
}
