<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weather', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('weather_location_id');
            $table->float('temp')->nullable();
            $table->float('feels_like')->nullable();
            $table->float('temp_min')->nullable();
            $table->float('temp_max')->nullable();
            $table->integer('pressure')->nullable();
            $table->integer('sea_level')->nullable();
            $table->integer('grnd_level')->nullable();
            $table->integer('humidity')->nullable();
            $table->float('temp_kf')->nullable();
            $table->string('main')->nullable();
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->float('wind_speed')->nullable();
            $table->integer('wind_deg')->nullable();
            $table->float('wind_gust')->nullable();
            $table->integer('visibility')->nullable();
            $table->timestamp('dt_txt')->nullable();
            $table->timestamps();

            $table->foreign('weather_location_id')->references('id')->on('weather_locations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('weathers');
    }
};
