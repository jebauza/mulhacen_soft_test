<?php

namespace App\Modules\Appointment\DTOs;

use App\Modules\Appointment\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Collection;

readonly class DateScheduleDTO
{
    const DATE = 'date';
    const APPOINTMENTS = 'appointments';

    /**
     * @param Carbon $date
     * @param Collection<int, Appointment> $appointments
     */
    public function __construct(
        public Carbon $date,
        public Collection $appointments
    ) {}
}
