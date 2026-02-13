<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\User\Models\User;
use App\Modules\Specialty\Models\Specialty;

class DentistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'first_name' => 'Roberto',
                'last_name' => 'García López',
                'specialties' => 'Ortodoncia',
            ],
            [
                'first_name' => 'Antonio',
                'last_name' => 'Sánchez Castro',
                'specialties' => 'Ortodoncia, Prótesis, Diagnosis',
            ],
            [
                'first_name' => 'Miguel',
                'last_name' => 'Díaz Romero',
                'specialties' => 'Cirugía, General',
            ],
            [
                'first_name' => 'Juan',
                'last_name' => 'Torres Navarro',
                'specialties' => 'Todas',
            ],
        ];

        $specialties = Specialty::pluck('id')->toArray();

        foreach ($items as $index => $item) {
            $firstName = $item['first_name'] ?? fake()->firstName();
            $lastName = $item['last_name'] ?? fake()->lastName();

            // Generate email from first_name and last_name
            $email = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $firstName)) . '.' . strtolower(preg_replace('/[^A-Za-z0-9]/', '', $lastName)) . '@example.com';

            $user = User::factory()->withPassword('0dHGgfh49v')->create([
                'email' => $email,
            ]);

            // create dentist via user relation (will set user_id)
            $dentist = $user->dentist()->create([
                'name' => $firstName,
                'last_name' => $lastName,
            ]);

            // attach specialties via relation
            if (mb_strtolower(trim($item['specialties'])) === 'todas') {
                $specialtyIds = $specialties;
            } else {
                $names = array_map('trim', explode(',', $item['specialties']));
                $specialtyIds = Specialty::whereIn('name', $names)->pluck('id')->toArray();
            }

            if (empty($specialtyIds) && ! empty($specialties)) {
                $randomSpecialties = fake()->randomElements($specialties, fake()->numberBetween(1, count($specialties)));
                $specialtyIds = $randomSpecialties;
            }

            if (! empty($specialtyIds)) {
                $dentist->specialties()->sync($specialtyIds);
            }
        }

        $this->command->info(self::class . ' finished');
    }
}
