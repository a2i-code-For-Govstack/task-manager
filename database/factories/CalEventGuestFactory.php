<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CalEventGuestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'event_id' => 1,
            'user_email' => $this->faker->email(),
            'visibility_type' => 'public',
            'user_type' => 'guest',
            'tag_color' => 'fc fc-event-success',
            'acceptance_status' => 'accepted',
        ];
    }
}
