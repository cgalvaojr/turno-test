<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeatherLocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'city' => $this->faker->city,
            'country' => $this->faker->countryCode(),
            'user_id' => 4 //User::factory(),
        ];
    }
}
