<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Poli;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Create Admin
        User::factory()->admin()->create();

        // Create Test Patient
        User::factory()->create([
            'name' => 'Pasien Test',
            'email' => 'pasien@example.com',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123',
            'role' => 'patient',
        ]);

        // Create Sample Patients
        User::factory(10)->create(['role' => 'patient']);

        // Create Polis
        $polis = Poli::factory(5)->create();

        // Create Doctors for each Poli
        $polis->each(function ($poli) {
            Doctor::factory(3)
                ->state(['poli_id' => $poli->id])
                ->create()
                ->each(function ($doctor) {
                    // Create schedules for doctors
                    $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                    foreach (array_slice($weekdays, 0, 5) as $day) {
                        Schedule::create([
                            'doctor_id' => $doctor->id,
                            'weekday' => $day,
                            'start_time' => '08:00',
                            'end_time' => '14:00',
                        ]);
                    }
                });
        });
    }
}
