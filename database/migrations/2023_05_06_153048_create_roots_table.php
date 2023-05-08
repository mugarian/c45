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
        Schema::create('roots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('node_id');
            $table->foreign('node_id')->references('id')->on('nodes');
            $table->string('nama')->unique();
            $table->integer('jumlah');
            $table->integer('no');
            $table->integer('yes');
            $table->float('entropy', 10, 10);
            $table->string('label')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roots');
    }
};
