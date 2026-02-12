<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
use Database\Seeders\AppointmentSeeder;
use Database\Seeders\DentistSeeder;
use Database\Seeders\PatientSeeder;
use Database\Seeders\SpecialtySeeder;
use Database\Seeders\TreatmentSeeder;
use Database\Seeders\UserFakeSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $this->call(UserFakeSeeder::class);

        // Seed specialties
        $this->call(SpecialtySeeder::class);
        // Seed treatments
        $this->call(TreatmentSeeder::class);
        // Seed dentists (requires specialties)
        $this->call(DentistSeeder::class);
        // Seed patients
        $this->call(PatientSeeder::class);
        // Seed appointments (requires patients and dentists)
        $this->call(AppointmentSeeder::class);
    }
}
