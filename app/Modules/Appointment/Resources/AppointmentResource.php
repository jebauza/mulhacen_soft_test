<?php

namespace App\Modules\Appointment\Resources;

use App\Modules\Appointment\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->{Appointment::ID},
            'patient_id' => $this->{Appointment::PATIENT_ID},
            'dentist_id' => $this->{Appointment::DENTIST_ID},
            'start' => $this->{Appointment::START}->toDateTimeString(),
            'end' => $this->{Appointment::END}->toDateTimeString(),
            'duration' => $this->{Appointment::DURATION},
            'reason' => $this->{Appointment::REASON},

            'treatment_ids' => $this->treatments()->pluck('id'),
        ];
    }
}
