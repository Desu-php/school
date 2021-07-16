<?php

namespace Database\Factories;

use App\Models\TaskCategorizeCategoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskCategorizeCategoryItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskCategorizeCategoryItem::class;
    protected $sum = 0;
    protected $categorize_category_id = 1;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if ($this->sum < 2){
            $this->sum ++;
        } else {
            $this->categorize_category_id += 1;
            $this->sum = 0;
        }
        return [
            'name' => $this->faker->text(15),
            'image' => $this->faker->imageUrl(400, 240),
            "categorize_category_id" => $this->categorize_category_id,
        ];
    }
}
