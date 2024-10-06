<?php

namespace App\Http\Controllers;

use App\Http\Resources\WeatherLocationResource;
use App\Http\Resources\WeatherResource;
use App\Services\Weather\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherService $service)
    {
    }

    public function index(Request $request): WeatherLocationResource
    {
        return new WeatherLocationResource($this->service->getWeather($request->user()->id));
    }

    public function store(Request $request)
    {
        $this->service->handle($request->country, $request->city);
        return response()->json(['message' => 'Weather data has been successfully stored'], JsonResponse::HTTP_CREATED);
    }
}
