<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeatherLocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'city',
        'country',
        'user_id',
    ];

    public function userLocationCount($userId): int
    {
        return $this->where('user_id', $userId)->count();
    }

    public function weathers(): HasMany
    {
        return $this->hasMany(Weather::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
