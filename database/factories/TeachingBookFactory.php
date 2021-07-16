<?php

namespace Database\Factories;

use App\Models\TeachingBook;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeachingBookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeachingBook::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(40),
            'description'=> $this->faker->text(300),
            'image'=> 'faker_book_cover.jpg',
            'audio'=> 'faker_book_audio.mp3',
            'file'=> 'faker_book_pdf.pdf',
        ];
    }
}
