<?php

namespace App\Http\Controllers;

use App\Http\Resources\WeatherLocationResource;
use App\Services\Weather\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WeatherController extends Controller
{
    public function __construct(private readonly WeatherService $service)
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $locationsData = $this->service->getWeather()->where('user_id', $request->user()->id)->get();
        return WeatherLocationResource::collection($locationsData);
    }

    public function show(int $id): AnonymousResourceCollection
    {
        $locationsData = $this->service->getWeather()->where('id', $id)->get();
        return WeatherLocationResource::collection($locationsData);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $this->service->createLocation($request->country, $request->city);
            return response()->json(['message' => 'Weather data has been successfully stored'], JsonResponse::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
        }
    }

    public function delete(Request $request): JsonResponse
    {
        $this->service->removeWheaterLocation($request->locationId);
        return response()->json(['message' => 'Weather data has been successfully deleted'], JsonResponse::HTTP_OK);
    }
}
