<?php

namespace Tests\Unit;

use App\Services\Weather\WeatherService;
use App\Http\Clients\WeatherHttpClient;
use App\Models\Weather;
use App\Models\WeatherLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use \Illuminate\Database\Eloquent\Builder;
use Mockery\MockInterface;
use Tests\TestCase;

class WeatherServiceTest extends TestCase
{
    use RefreshDatabase;

    private WeatherService $weatherService;
    private MockInterface $weatherHttpClientMock;
    private MockInterface $weatherLocationMock;
    private MockInterface $weatherMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->weatherHttpClientMock = $this->mock(WeatherHttpClient::class);
        $this->weatherLocationMock = $this->mock(WeatherLocation::class);
        $this->weatherMock = $this->mock(Weather::class);

        $this->weatherService = new WeatherService(
            $this->weatherHttpClientMock,
            $this->weatherLocationMock,
            $this->weatherMock
        );
    }

    public function testShouldGetWeather()
    {
        $builderMock = \Mockery::mock(Builder::class);

        $this->weatherLocationMock
            ->shouldReceive('with')
            ->once()
            ->with('weathers')
            ->andReturn($builderMock);

        $result = $this->weatherService->getWeather();

        $this->assertInstanceOf(Builder::class, $result);
    }

    public function testShouldRemoveWeatherLocation()
    {
        $locationId = 1;

        $this->weatherLocationMock
            ->shouldReceive('find')
            ->once()
            ->with($locationId)
            ->andReturnSelf();

        $this->weatherLocationMock
            ->shouldReceive('delete')
            ->once()
            ->andReturn(true);

        $result = $this->weatherService->removeWheaterLocation($locationId);

        $this->assertTrue($result);
    }

    public function testShouldFetchWeatherFromApi()
    {
        $country = 'US';
        $city = 'New York';
        $apiResponse = (object)['list' => []];

        $this->weatherHttpClientMock
            ->shouldReceive('get')
            ->once()
            ->with('/data/2.5/forecast', ['q' => "$city,$country", 'units' => 'imperial'])
            ->andReturn($apiResponse);

        $result = $this->weatherService->fetchWeatherFromApi($country, $city);

        $this->assertEquals($apiResponse, $result);
    }

    public function testShouldCreateLocation()
    {
        $country = 'US';
        $city = 'New York';
        $apiResponse = (object)['list' => []];
        $filteredData = [];
        $parsedData = [];

        $this->weatherHttpClientMock
            ->shouldReceive('get')
            ->once()
            ->with('/data/2.5/forecast', ['q' => "$city,$country", 'units' => 'imperial'])
            ->andReturn($apiResponse);

        $this->weatherLocationMock
            ->shouldReceive('where')
            ->with('city', $city)
            ->andReturnSelf();

        $this->weatherLocationMock
            ->shouldReceive('where')
            ->with('country', $country)
            ->andReturnSelf();

        $this->weatherLocationMock
            ->shouldReceive('where')
            ->with('user_id', auth()->id())
            ->andReturnSelf();

        $this->weatherLocationMock
            ->shouldReceive('first')
            ->andReturn(null);

        $this->weatherLocationMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)['id' => 1]);

        $this->weatherLocationMock
            ->shouldReceive('userLocationCount')
            ->once()
            ->with(auth()->id())
            ->andReturn(2);

        $this->weatherMock
            ->shouldReceive('insert')
            ->once()
            ->with($parsedData);

        DB::shouldReceive('transaction')
            ->once()
            ->andReturnUsing(function ($callback) use ($filteredData, $country, $city) {
                $callback();
            });

        $this->weatherService->createLocation($country, $city);
    }

    public function testShouldFilterWeatherDataByDate()
    {
        $apiData = (object)[
            'list' => [
                (object)['dt_txt' => now()->toDateTimeString()],
                (object)['dt_txt' => now()->addDays(1)->toDateTimeString()],
                (object)['dt_txt' => now()->addDays(3)->toDateTimeString()],
            ]
        ];

        $result = $this->weatherService->filterWeatherDataByDate($apiData);

        $this->assertCount(2, $result);
    }

    public function testShouldCheckLocationExists()
    {
        $country = 'US';
        $city = 'New York';

        $this->weatherLocationMock
            ->shouldReceive('where')
            ->once()
            ->with('city', $city)
            ->andReturnSelf();

        $this->weatherLocationMock
            ->shouldReceive('where')
            ->once()
            ->with('country', $country)
            ->andReturnSelf();

        $this->weatherLocationMock
            ->shouldReceive('where')
            ->once()
            ->with('user_id', auth()->id())
            ->andReturnSelf();

        $this->weatherLocationMock
            ->shouldReceive('first')
            ->once()
            ->andReturn(null);

        $this->weatherService->checkLocationExists($country, $city);
    }

    public function testShouldUserCanCreateLocations()
    {
        $this->weatherLocationMock
            ->shouldReceive('userLocationCount')
            ->once()
            ->with(auth()->id())
            ->andReturn(2);

        $result = $this->weatherService->userCanCreateLocations();

        $this->assertTrue($result);
    }

    public function testShouldParseApiDataForDatabase()
    {
        $apiData = [
            (object)[
                'main' => (object)[
                    'temp' => 75.0,
                    'feels_like' => 74.0,
                    'temp_min' => 70.0,
                    'temp_max' => 80.0,
                    'pressure' => 1012,
                    'sea_level' => 1012,
                    'grnd_level' => 1000,
                    'humidity' => 60,
                    'temp_kf' => 0.5,
                ],
                'weather' => [
                    (object)[
                        'main' => 'Clear',
                        'description' => 'clear sky',
                        'icon' => '01d',
                    ]
                ],
                'wind' => (object)[
                    'speed' => 5.0,
                    'deg' => 180,
                    'gust' => 7.0,
                ],
                'visibility' => 10000,
                'dt_txt' => '2024-10-07 12:00:00',
            ]
        ];

        $locationId = 1;

        $expectedResult = [
            [
                'weather_location_id' => $locationId,
                'temp' => 75.0,
                'feels_like' => 74.0,
                'temp_min' => 70.0,
                'temp_max' => 80.0,
                'pressure' => 1012,
                'sea_level' => 1012,
                'grnd_level' => 1000,
                'humidity' => 60,
                'temp_kf' => 0.5,
                'main' => 'Clear',
                'description' => 'clear sky',
                'icon' => '01d',
                'wind_speed' => 5.0,
                'wind_deg' => 180,
                'wind_gust' => 7.0,
                'visibility' => 10000,
                'dt_txt' => '2024-10-07 12:00:00',
            ]
        ];

        $result = $this->weatherService->parseApiDataForDatabase($apiData, $locationId);

        $this->assertEquals($expectedResult, $result);
    }
}
