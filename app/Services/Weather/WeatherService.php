<?php

namespace App\Services\Weather;

use App\Http\Clients\WeatherHttpClient;

class WeatherService
{
    private const string API_ENDPOINT = '/data/2.5/forecast';
    public function __construct(private readonly WeatherHttpClient $client)
    {
    }

    public function handle(string $country, string $city): array
    {
        $apiResult = $this->client->get(self::API_ENDPOINT, ['q' => "$city,$country"]);
        dd($apiResult);
    }

    public function getWeatherFromApi()
    {

    }
}
