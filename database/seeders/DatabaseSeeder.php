<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Bahan;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //1
        Bahan::create([
            'outlook' => 'sunny',
            'temperature' => 'hot',
            'humidity' => 'high',
            'windy' => 'false',
            'play' => 'no'
        ]);

        //2
        Bahan::create([
            'outlook' => 'sunny',
            'temperature' => 'hot',
            'humidity' => 'high',
            'windy' => 'true',
            'play' => 'no'
        ]);

        //3
        Bahan::create([
            'outlook' => 'cloudy',
            'temperature' => 'hot',
            'humidity' => 'high',
            'windy' => 'false',
            'play' => 'yes'
        ]);

        //4
        Bahan::create([
            'outlook' => 'rainy',
            'temperature' => 'mild',
            'humidity' => 'high',
            'windy' => 'false',
            'play' => 'yes'
        ]);

        //5
        Bahan::create([
            'outlook' => 'rainy',
            'temperature' => 'cool',
            'humidity' => 'normal',
            'windy' => 'false',
            'play' => 'yes'
        ]);

        //6
        Bahan::create([
            'outlook' => 'rainy',
            'temperature' => 'cool',
            'humidity' => 'normal',
            'windy' => 'true',
            'play' => 'yes'
        ]);

        //7
        Bahan::create([
            'outlook' => 'cloudy',
            'temperature' => 'cool',
            'humidity' => 'normal',
            'windy' => 'true',
            'play' => 'yes'
        ]);

        //8
        Bahan::create([
            'outlook' => 'sunny',
            'temperature' => 'mild',
            'humidity' => 'high',
            'windy' => 'false',
            'play' => 'no'
        ]);

        //9
        Bahan::create([
            'outlook' => 'sunny',
            'temperature' => 'cool',
            'humidity' => 'normal',
            'windy' => 'false',
            'play' => 'yes'
        ]);

        //10
        Bahan::create([
            'outlook' => 'rainy',
            'temperature' => 'mild',
            'humidity' => 'normal',
            'windy' => 'false',
            'play' => 'yes'
        ]);

        //11
        Bahan::create([
            'outlook' => 'sunny',
            'temperature' => 'mild',
            'humidity' => 'normal',
            'windy' => 'true',
            'play' => 'yes'
        ]);

        //12
        Bahan::create([
            'outlook' => 'cloudy',
            'temperature' => 'mild',
            'humidity' => 'high',
            'windy' => 'true',
            'play' => 'yes'
        ]);

        //13
        Bahan::create([
            'outlook' => 'cloudy',
            'temperature' => 'hot',
            'humidity' => 'normal',
            'windy' => 'false',
            'play' => 'yes'
        ]);

        //14
        Bahan::create([
            'outlook' => 'rainy',
            'temperature' => 'mild',
            'humidity' => 'high',
            'windy' => 'true',
            'play' => 'no'
        ]);
    }
}
