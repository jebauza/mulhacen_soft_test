<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TreatmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ['name' => 'Brackets', 'specialty' => 'Ortodoncia', 'base' => '3999.95', 'duration' => 45],
            ['name' => 'Composite', 'specialty' => 'Prótesis', 'base' => '680.00', 'duration' => 60],
            ['name' => 'Exp. Maxilar', 'specialty' => 'Cirugía', 'base' => '9000.00', 'duration' => 120],
            ['name' => 'Radiografía panorámica', 'specialty' => 'Diagnosis', 'base' => '50.00', 'duration' => 10],
            ['name' => 'Blanqueamiento', 'specialty' => 'General', 'base' => '199.62', 'duration' => 20],
        ];

        foreach ($items as $item) {
            $specialtyId = DB::table('specialties')->where('name', $item['specialty'])->value('id');

            DB::table('treatments')->insert([
                'id' => (string) Str::uuid(),
                'specialty_id' => $specialtyId,
                'name' => $item['name'],
                'base_amount' => $item['base'],
                'duration' => $item['duration'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
