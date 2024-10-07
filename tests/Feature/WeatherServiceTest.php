<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeatherLocation;
use App\Services\Weather\WeatherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherServiceTest extends TestCase
{
    use RefreshDatabase;

    protected WeatherService $weatherService;

    public function setUp(): void
    {
        parent::setUp();
        $this->weatherService = $this->app->make(WeatherService::class);
    }

    public function testCreateLocation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $country = 'US';
        $city = 'New York';

        $this->weatherService->createLocation($country, $city);

        $this->assertDatabaseHas('weather_locations', [
            'user_id' => $user->id,
            'city' => $city,
            'country' => $country,
        ]);
    }

    public function testCheckLocationExists()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        WeatherLocation::factory()->create([
            'user_id' => $user->id,
            'city' => 'New York',
            'country' => 'US',
        ]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Location already exists');

        $this->weatherService->checkLocationExists('US', 'New York');
    }

    public function testUserCanCreateLocations()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        WeatherLocation::factory()->count(2)->create(['user_id' => $user->id]);

        $this->assertTrue($this->weatherService->userCanCreateLocations());

        WeatherLocation::factory()->create(['user_id' => $user->id]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('User has reached the limit of locations');

        $this->weatherService->userCanCreateLocations();
    }

    public function testShoulWeatherdApiReturnCorrectFields()
    {
        $result = $this->weatherService->fetchWeatherFromApi('BR', 'Brasilia');

        $this->assertObjectHasProperty('cod', $result);
        $this->assertObjectHasProperty('list', $result);
        $this->assertObjectHasProperty('message', $result);
        $this->assertObjectHasProperty('cnt', $result);
        $this->assertObjectHasProperty('dt', $result->list[0]);
        $this->assertObjectHasProperty('main', $result->list[0]);
        $this->assertObjectHasProperty('clouds', $result->list[0]);
        $this->assertObjectHasProperty('wind', $result->list[0]);
        $this->assertObjectHasProperty('dt_txt', $result->list[0]);
        $this->assertObjectHasProperty('visibility', $result->list[0]);
        $this->assertObjectHasProperty('temp', $result->list[0]->main);
        $this->assertObjectHasProperty('feels_like', $result->list[0]->main);
        $this->assertObjectHasProperty('temp_min', $result->list[0]->main);
        $this->assertObjectHasProperty('temp_max', $result->list[0]->main);
        $this->assertObjectHasProperty('pressure', $result->list[0]->main);
        $this->assertObjectHasProperty('sea_level', $result->list[0]->main);
        $this->assertObjectHasProperty('grnd_level', $result->list[0]->main);
        $this->assertObjectHasProperty('humidity', $result->list[0]->main);
    }
}
