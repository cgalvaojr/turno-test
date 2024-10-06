<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Weather extends Model
{
    use HasFactory;

    protected $fillable = [
        'weather_location_id',
        'temp',
        'feels_like',
        'temp_min',
        'temp_max',
        'pressure',
        'sea_level',
        'grnd_level',
        'humidity',
        'temp_kf',
        'main',
        'description',
        'icon',
        'wind_speed',
        'wind_deg',
        'wind_gust',
        'visibility',
        'dt_txt',
    ];

    public function weatherLocation(): BelongsTo
    {
        return $this->belongsTo(WeatherLocation::class);
    }
}
