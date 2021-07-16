<?php

namespace Database\Factories;

use App\Models\TeachingAudio;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeachingAudioFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TeachingAudio::class;

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
            'audio'=> 'faker_book_audio.mp3',
        ];
    }
}
