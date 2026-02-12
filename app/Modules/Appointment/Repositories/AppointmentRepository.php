<?php

namespace App\Modules\Appointment\Repositories;

use App\Modules\Patient\Models\Patient;
use App\Common\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\Appointment\Models\Appointment;

class AppointmentRepository extends BaseRepository
{
    public function __construct(Patient $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return Appointment::with([
            'patient',
            'dentist',
            'treatments',
        ])->get();
    }

    public function assignTreatments(Appointment|string $appointment, array $treatmentsIds): Appointment
    {
        if (is_string($appointment)) {
            $appointment = $this->findOrFail($appointment);
        }

        $appointment->treatments()->attach($treatmentsIds);

        return $appointment->load('treatments');
    }
}
