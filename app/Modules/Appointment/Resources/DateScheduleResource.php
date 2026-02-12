<?php

namespace App\Modules\Appointment\Resources;

use App\Modules\Appointment\DTOs\DateScheduleDTO;
use App\Modules\Appointment\Models\Appointment;
use App\Modules\Appointment\Resources\AppointmentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use InvalidArgumentException;

/**
 * @property-read DateScheduleDTO $resource
 */
class DateScheduleResource extends JsonResource
{
    public function __construct($resource)
    {
        if (!$resource instanceof DateScheduleDTO) {
            throw new InvalidArgumentException('Expected resource to be an instance of DateScheduleDTO.');
        }
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'date' => $this->{DateScheduleDTO::DATE}->toDateString(),
            'appointments' => $this->{DateScheduleDTO::APPOINTMENTS}->map(function (Appointment $item) {
                return new AppointmentResource($item);
            }),
        ];
    }
}
