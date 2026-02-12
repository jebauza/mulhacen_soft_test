<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Appointment\Models\Appointment;
use App\Modules\Patient\Models\Patient;
use App\Modules\User\Models\Dentist;
use App\Modules\Treatment\Models\Treatment;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patientIds = Patient::pluck('id')->toArray();
        $dentistIds = Dentist::pluck('id')->toArray();
        $treatmentIds = Treatment::pluck('id')->toArray();

        if (empty($patientIds) || empty($dentistIds) || empty($treatmentIds)) {
            $this->command->warn('No patients, dentists, or treatments found. Run their seeders first.');
            return;
        }

        $durations = [10, 20, 30, 45, 60, 120];

        // create 15 appointments
        for ($i = 0; $i < 15; $i++) {
            $patientId = $patientIds[array_rand($patientIds)];
            $dentistId = $dentistIds[array_rand($dentistIds)];
            $duration = $durations[array_rand($durations)];

            $start = Carbon::now()->addDays(rand(0, 14))->setTime(rand(8, 17), [0, 15, 30, 45][array_rand([0, 1, 2, 3])]);
            $end = (clone $start)->addMinutes($duration);

            $appointment = Appointment::create([
                'patient_id' => $patientId,
                'dentist_id' => $dentistId,
                'start' => $start,
                'end' => $end,
                'duration' => $duration,
            ]);

            // attach 1-3 random treatments to this appointment
            $numTreatments = rand(1, min(3, count($treatmentIds)));
            $randomTreatments = array_rand($treatmentIds, $numTreatments);
            $treatmentsToAttach = is_array($randomTreatments) ? array_values(array_map(fn($k) => $treatmentIds[$k], $randomTreatments)) : [$treatmentIds[$randomTreatments]];
            $appointment->treatments()->sync($treatmentsToAttach);
        }

        $this->command->info(self::class . ' finished');
    }
}
