<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weathers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('weather_location_id');
            $table->float('temp');
            $table->float('feels_like');
            $table->float('temp_min');
            $table->float('temp_max');
            $table->integer('pressure');
            $table->integer('sea_level');
            $table->integer('grnd_level');
            $table->integer('humidity');
            $table->float('temp_kf');
            $table->string('main');
            $table->string('description');
            $table->string('icon');
            $table->float('wind_speed');
            $table->integer('wind_deg');
            $table->float('wind_gust');
            $table->integer('visibility');
            $table->timestamp('dt_txt');
            $table->timestamps();

            $table->foreign('weather_location_id')->references('id')->on('weather_locations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('weathers');
    }
};
