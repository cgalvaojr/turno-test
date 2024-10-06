<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('weather_locations', function (Blueprint $table) {
            $table->id();
            $table->string('city');
            $table->string('country');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'city', 'country']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('weather_locations');
    }
};
