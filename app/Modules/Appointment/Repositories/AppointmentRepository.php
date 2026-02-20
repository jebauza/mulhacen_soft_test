<?php

namespace App\Modules\Appointment\Repositories;

use App\Common\Repositories\BaseRepository;
use App\Modules\Appointment\Models\Appointment;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;

class AppointmentRepository extends BaseRepository
{
    public function __construct(Appointment $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return Appointment::with([
            // 'patient',
            // 'dentist',
            'treatments',
        ])
            ->orderBy(Appointment::START)
            ->get();
    }

    public function getScheduledAppointments(): Collection
    {
        return $this->model->with([
            'treatments',
        ])
            ->whereDate(Appointment::START, '>=', CarbonImmutable::now())
            ->orderBy(Appointment::START)
            ->get();
    }

    public function assignTreatments(Appointment|string $appointment, array $treatmentsIds): Appointment
    {
        if (is_string($appointment)) {
            $appointment = $this->findOrFail($appointment);
        }

        $appointment->treatments()->attach($treatmentsIds);

        return $appointment;
    }
}
