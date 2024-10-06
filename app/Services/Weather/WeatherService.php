<?php

namespace App\Services\Weather;

use App\Http\Clients\WeatherHttpClient;
use App\Models\Weather;
use App\Models\WeatherLocation;

class WeatherService
{
    private const string API_ENDPOINT = '/data/2.5/forecast';
    public function __construct(
        private readonly WeatherHttpClient $client,
        private readonly WeatherLocation $model
    )
    {
    }

    public function getWeather(int $userId)
    {
        return $this->model->with('weathers')->where('user_id', $userId)->first();
    }

    public function storeWeather(string $country, string $city): array
    {
        $apiResult = $this->fetchWeatherFromApi($country, $city);
        $parsedApiData = $this->parseApiDataForDatabase($apiResult);
        $this->storeWeather($apiResult);
    }

    public function fetchWeatherFromApi(string $country, string $city): array
    {
        return $this->client->get(self::API_ENDPOINT, ['q' => "$city,$country"]);
    }

    private function parseApiDataForDatabase(array $apiData): array
    {
        return [
            'weather' => [

            ],
            'location' => [

            ]
        ];
    }


}
