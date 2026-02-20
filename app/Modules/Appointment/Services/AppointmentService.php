<?php

namespace App\Modules\Appointment\Services;

use App\Modules\Appointment\DTOs\CreateAppointmentDTO;
use App\Modules\Appointment\DTOs\DateScheduleDTO;
use App\Modules\Appointment\Models\Appointment;
use App\Modules\Appointment\Repositories\AppointmentRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;

class AppointmentService
{
    public function __construct(
        protected readonly AppointmentRepository $appointmentRepo
    ) {}

    /**
     * @return Collection<int, DateScheduleDTO>
     */
    public function schedule(): Collection
    {
        Gate::authorize('appointment.schedule');

        $data = $this->appointmentRepo->getScheduledAppointments()
            ->groupBy(function ($appointment) {
                return $appointment->{Appointment::START}->format('Y-m-d');
            })
            ->map(function (Collection $appointments, string $date) {
                return new DateScheduleDTO(
                    Carbon::create($date),
                    $appointments
                );
            })
            ->values();

        return $data;
    }

    public function create(CreateAppointmentDTO $dto): Appointment
    {
        Gate::authorize('appointment.create');

        $appointment = $this->appointmentRepo->create($dto->toArray(true));

        $appointment = $this->appointmentRepo->assignTreatments(
            $appointment,
            $dto->{CreateAppointmentDTO::TREATMENT_IDS}
        );

        return $this->appointmentRepo->load($appointment, ['treatments']);
    }
}
