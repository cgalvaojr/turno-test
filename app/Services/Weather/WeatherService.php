<?php

namespace App\Services\Weather;

use App\Http\Clients\WeatherHttpClient;
use App\Models\Weather;
use App\Models\WeatherLocation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function getWeather(): Builder
    {
        return $this->weatherLocationModel->with('weathers');
    }

    public function removeWheaterLocation($locationId)
    {
        return $this->weatherLocationModel->find($locationId)->delete();
    }

    public function fetchWeatherFromApi(string $country, string $city): object
    {
        return $this->client->get(self::API_ENDPOINT, ['q' => "$city,$country", 'units' => 'imperial']);
    }

    public function createLocation(string $country, string $city): void
    {
        $this->checkLocationExists($country, $city);
        if($this->userCanCreateLocations()) {
            $apiResult = $this->fetchWeatherFromApi($country, $city);
            $filteredData = $this->filterWeatherDataByDate($apiResult);
            DB::transaction(function () use ($filteredData, $country, $city) {
                $weatherLocation = $this->weatherLocationModel->create([
                    'user_id' => auth()->id(),
                    'city' => $city,
                    'country' => $country,
                ]);

                $parsedApiData = $this->parseApiDataForDatabase($filteredData, $weatherLocation->id);
                $this->weatherModel->insert($parsedApiData);
            });
        }
    }

    public function checkLocationExists(string $country, string $city): void
    {
        $location = $this->weatherLocationModel
            ->where('city', $city)
            ->where('country', $country)
            ->where('user_id', auth()->id())
            ->first();
        if($location) {
            throw new \RuntimeException('Location already exists');
        }
    }

    public function filterWeatherDataByDate(object $apiData): array
    {
        $filteredData = [];
        $today = Carbon::today()->startOfDay();
        $dayAfterTomorrow = Carbon::today()->addDays(2)->endOfDay();

        foreach ($apiData->list as $entry) {
            $entryDate = Carbon::parse($entry->dt_txt);
            if ($entryDate->between($today, $dayAfterTomorrow)) {
                $filteredData[] = $entry;
            }
        }

        return $filteredData;
    }

    public function userCanCreateLocations(): bool
    {
        $locationCount = $this->weatherLocationModel->userLocationCount(auth()->id());
        if($locationCount < self::MAX_LOCATIONS_PER_USER) {
            return true;
        }
        throw new \RuntimeException('User has reached the limit of locations');
    }
    public function parseApiDataForDatabase(array $apiData, int $locationId): array
    {
        $weatherData = [];

        foreach ($apiData as $entry) {
            $weatherData[] = [
                'weather_location_id' => $locationId,
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
