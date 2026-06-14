<?php

namespace Database\Factories;

use App\Models\Poli;
use Illuminate\Database\Eloquent\Factories\Factory;

class PoliFactory extends Factory
{
    protected $model = Poli::class;

    public function definition(): array
    {
        $names = ['Poli Umum', 'Poli Gigi', 'Poli Ibu dan Anak', 'Poli KB', 'Poli Laboratorium'];
        
        return [
            'name' => fake()->randomElement($names),
            'description' => fake()->sentence(),
        ];
    }
}
