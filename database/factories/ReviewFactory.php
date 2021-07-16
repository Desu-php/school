<?php

namespace Database\Factories;

use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Review::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'full_name' => $this->faker->name,
            'email' => $this->faker->email,
            'rating' => $this->faker->numberBetween(1, 5),
            'text' => $this->faker->text(800),
            'status' => $this->faker->numberBetween(1, 3),
            'user_id' => $this->faker->numberBetween(1, 50),
        ];
    }
}
