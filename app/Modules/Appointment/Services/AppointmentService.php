<?php

namespace App\Modules\Appointment\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use App\Modules\Appointment\Models\Appointment;
use App\Modules\Appointment\DTOs\CreateAppointmentDTO;
use App\Modules\Appointment\Repositories\AppointmentRepository;

class AppointmentService
{
    public function __construct(
        protected readonly AppointmentRepository $appointmentRepo
    ) {}

    public function index()
    {
        Gate::authorize('appointment.index');

        $data = $this->appointmentRepo->all()
            ->groupBy('date');

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

        return $appointment;
    }
}
