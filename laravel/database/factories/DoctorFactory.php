<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\Poli;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        $specialties = ['Dokter Umum', 'Dokter Gigi', 'Dokter Anak', 'Dokter Mata', 'Dokter Kandungan'];
        
        return [
            'poli_id' => Poli::factory(),
            'name' => fake()->name(),
            'specialty' => fake()->randomElement($specialties),
            'experience_years' => fake()->numberBetween(2, 20),
            'daily_quota' => 20,
        ];
    }
}
