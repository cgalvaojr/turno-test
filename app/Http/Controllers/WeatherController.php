<?php

namespace App\Http\Controllers;

use App\Services\Weather\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherService $service)
    {
    }

    public function getWeather(Request $request): JsonResponse
    {
        return response()->json($this->service->handle($request->country, $request->city));
    }
}
