<?php

namespace Database\Factories;

use App\Models\OtherMaterial;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtherMaterialFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OtherMaterial::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(40),
            'description'=> $this->faker->text(300)
        ];
    }
}
