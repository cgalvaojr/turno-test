<?php

namespace Database\Factories;

use App\Models\Weather;
use App\Models\WeatherLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

class WeatherFactory extends Factory
{
    protected $model = Weather::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'weather_location_id' => WeatherLocation::factory()->create()->id,
            'temp' => $this->faker->randomFloat(2, -30, 50),
            'feels_like' => $this->faker->randomFloat(2, -30, 50),
            'temp_min' => $this->faker->randomFloat(2, -30, 50),
            'temp_max' => $this->faker->randomFloat(2, -30, 50),
            'pressure' => $this->faker->numberBetween(950, 1050),
            'sea_level' => $this->faker->numberBetween(950, 1050),
            'grnd_level' => $this->faker->numberBetween(950, 1050),
            'humidity' => $this->faker->numberBetween(0, 100),
            'temp_kf' => $this->faker->randomFloat(2, -10, 10),
            'main' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'icon' => $this->faker->word(),
            'wind_speed' => $this->faker->randomFloat(2, 0, 100),
            'wind_deg' => $this->faker->numberBetween(0, 360),
            'wind_gust' => $this->faker->randomFloat(2, 0, 100),
            'visibility' => $this->faker->numberBetween(0, 10000),
            'dt_txt' => $this->faker->dateTime(),
        ];
    }
}
