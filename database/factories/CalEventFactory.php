<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CalEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'event_title_en' => $this->faker->text(10),
            'event_title_bn' => $this->faker->text(10),
            'event_description' => $this->faker->text(50),
            'event_start_date_time' => $this->faker->dateTimeThisYear(),
            'event_end_date_time' => $this->faker->dateTimeThisYear(),
            'event_location' => $this->faker->city(),
            'event_type' => 'workshop',
            'event_previous_link' => '',
            'status' => 'active',
        ];
    }
}
