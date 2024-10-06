<?php

namespace App\Http\Controllers;

use App\Http\Resources\WeatherLocationResource;
use App\Http\Resources\WeatherResource;
use App\Services\Weather\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use phpDocumentor\Reflection\Types\Collection;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $locationsData = $this->service->getWeather($request->user()->id);
        return WeatherLocationResource::collection($locationsData);
    }

    public function store(Request $request)
    {
        $this->service->handle($request->country, $request->city);
        return response()->json(['message' => 'Weather data has been successfully stored'], JsonResponse::HTTP_CREATED);
    }

    public function delete(Request $request): JsonResponse
    {
        $this->service->removeWheaterLocation($request->locationId);
        return response()->json(['message' => 'Weather data has been successfully deleted'], JsonResponse::HTTP_OK);
    }
}
