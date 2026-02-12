<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Specialty\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            'Ortodoncia',
            'Prótesis',
            'Cirugía',
            'Diagnosis',
            'General',
        ];

        foreach ($specialties as $name) {
            Specialty::firstOrCreate(['name' => $name]);
        }
    }
}
