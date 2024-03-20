<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CalEventPreferredGuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_email' => 'ismail4g@gmail.com',
            'preferred_email' => $this->faker->email(),
            'preferred_name_en' => $this->faker->name(),
            'preferred_name_bn' => $this->faker->name(),
        ];
    }
}
