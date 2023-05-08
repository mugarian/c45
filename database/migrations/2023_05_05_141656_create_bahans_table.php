<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bahans', function (Blueprint $table) {
            $table->id();
            $table->enum('outlook', ['sunny', 'cloudy', 'rainy']);
            $table->enum('temperature', ['hot', 'mild', 'cool']);
            $table->enum('humidity', ['high', 'normal']);
            $table->enum('windy', ['true', 'false']);
            $table->enum('play', ['no', 'yes']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahans');
    }
};
