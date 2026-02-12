<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        foreach ($items as $index => $item) {
            // create unique email
            $email = strtolower(preg_replace('/[^A-Za-z0-9]/', '', $item['first_name'])) . '.' . strtolower(preg_replace('/[^A-Za-z0-9]/', '', explode(' ', $item['last_name'])[0])) . '@example.com';
            // ensure uniqueness
            $baseEmail = $email;
            $i = 1;
            while (DB::table('users')->where('email', $email)->exists()) {
                $email = str_replace('@', "{$i}@", $baseEmail);
                $i++;
            }

            $user = User::factory()->withPassword('password')->create([
                'email' => $email,
            ]);

            // create dentist via user relation (will set user_id)
            $dentist = $user->dentist()->create([
                'name' => $item['first_name'],
                'last_name' => $item['last_name'],
            ]);

            // attach specialties via relation
            if (mb_strtolower(trim($item['specialties'])) === 'todas') {
                $specialtyIds = Specialty::pluck('id')->toArray();
            } else {
                $names = array_map('trim', explode(',', $item['specialties']));
                $specialtyIds = Specialty::whereIn('name', $names)->pluck('id')->toArray();
            }

            if (! empty($specialtyIds)) {
                $dentist->specialties()->sync($specialtyIds);
            }
        }

        $this->command->info(self::class . ' finished');
    }
}
