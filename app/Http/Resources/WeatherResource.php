<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeatherResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'weather_location_id' => $this->weather_location_id,
            'temp' => $this->temp,
            'feels_like' => $this->feels_like,
            'temp_min' => $this->temp_min,
            'temp_max' => $this->temp_max,
            'pressure' => $this->pressure,
            'sea_level' => $this->sea_level,
            'grnd_level' => $this->grnd_level,
            'humidity' => $this->humidity,
            'temp_kf' => $this->temp_kf,
            'main' => $this->main,
            'description' => $this->description,
            'icon' => $this->icon,
            'wind_speed' => $this->wind_speed,
            'wind_deg' => $this->wind_deg,
            'wind_gust' => $this->wind_gust,
            'visibility' => $this->visibility,
            'dt_txt' => $this->dt_txt,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
