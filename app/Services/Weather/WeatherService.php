<?php

namespace App\Services\Weather;

use App\Http\Clients\WeatherHttpClient;
use App\Models\Weather;
use App\Models\WeatherLocation;
use Illuminate\Support\Facades\DB;

class WeatherService
{
    private const string API_ENDPOINT = '/data/2.5/forecast';
    private const int MAX_LOCATIONS_PER_USER = 3;

    public function __construct(
        private readonly WeatherHttpClient $client,
        private readonly WeatherLocation $weatherLocationModel,
        private readonly Weather $weatherModel
    )
    {
    }

    public function getWeather(int $userId)
    {
        return $this->weatherLocationModel->with('weathers')->where('user_id', $userId)->get();
    }

    public function removeWheaterLocation($locationId)
    {
        return $this->weatherLocationModel->find($locationId)->delete();
    }

    public function fetchWeatherFromApi(string $country, string $city): object
    {
        return $this->client->get(self::API_ENDPOINT, ['q' => "$city,$country"]);
    }

    public function createLocation(string $country, string $city): void
    {
        if($this->userCanCreateLocations()) {
            $apiResult = $this->fetchWeatherFromApi($country, $city);
            $parsedApiData = $this->parseApiDataForDatabase($apiResult);

            DB::transaction(function () use ($parsedApiData, $country, $city) {
                $weatherLocation = $this->weatherLocationModel->create([
                    'user_id' => auth()->id(),
                    'city' => $city,
                    'country' => $country,
                ]);

                $this->weatherModel->create([
                    'weather_location_id' => $weatherLocation->id,
                    $parsedApiData
                ]);
            });
        }
    }

    private function userCanCreateLocations(): bool
    {
        $locationCount = $this->weatherLocationModel->userLocationCount(auth()->id());
        if($locationCount < self::MAX_LOCATIONS_PER_USER) {
            return true;
        }
        throw new \RuntimeException('User has reached the limit of locations');
    }
    private function parseApiDataForDatabase(object $apiData): array
    {
        $weatherData = [];

        foreach ($apiData->list as $entry) {
            $weatherData[] = [
                'dt' => $entry->dt,
                'temp' => $entry->main->temp,
                'feels_like' => $entry->main->feels_like,
                'temp_min' => $entry->main->temp_min,
                'temp_max' => $entry->main->temp_max,
                'pressure' => $entry->main->pressure,
                'sea_level' => $entry->main->sea_level,
                'grnd_level' => $entry->main->grnd_level,
                'humidity' => $entry->main->humidity,
                'temp_kf' => $entry->main->temp_kf,
                'main' => $entry->weather[0]->main,
                'description' => $entry->weather[0]->description,
                'icon' => $entry->weather[0]->icon,
                'wind_speed' => $entry->wind->speed,
                'wind_deg' => $entry->wind->deg,
                'wind_gust' => $entry->wind->gust,
                'visibility' => $entry->visibility,
                'dt_txt' => $entry->dt_txt,
            ];
        }

        return $weatherData;
    }


}
