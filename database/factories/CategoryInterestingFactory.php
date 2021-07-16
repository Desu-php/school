<?php

namespace Database\Factories;

use App\Models\CategoryInteresting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryInterestingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CategoryInteresting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $title = $this->faker->text(50);
        $slug = Str::slug($title, '-');

        return [
            'title' => $title,
            'slug' => $slug,
            'seo_title' => $this->faker->text(50),
            'seo_description' => $this->faker->text(100),
            'color' => 'linear-gradient(90deg, rgb(227, 52, 47) 0px, rgb(255, 237, 74))'
        ];
    }
}
