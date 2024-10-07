<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WeatherLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeatherFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'sanctum');
    }

    public function testShouldDisplayLocationsCorrectly()
    {
        WeatherLocation::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/weather');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'city', 'country', 'user_id', 'created_at', 'updated_at'
                    ]
                ]
            ]);
    }

    public function testShouldDisplayForecastCorrectly()
    {
        $location = WeatherLocation::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/weather/{$location->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'city', 'country', 'user_id', 'created_at', 'updated_at'
                    ]
                ]
            ]);
    }

    public function testShouldStoreLocationsCorrectly()
    {
        $data = [
            'country' => 'US',
            'city' => 'New York'
        ];

        $response = $this->postJson('/api/weather', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Weather data has been successfully stored'
            ]);

        $this->assertDatabaseHas('weather_locations', [
            'user_id' => $this->user->id,
            'city' => 'New York',
            'country' => 'US'
        ]);
    }

    public function testShouldRemoveLocationsCorrectly()
    {
        $location = WeatherLocation::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/weather/{$location->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Weather data has been successfully deleted'
            ]);

        $this->assertDatabaseMissing('weather_locations', [
            'id' => $location->id
        ]);
    }
}
