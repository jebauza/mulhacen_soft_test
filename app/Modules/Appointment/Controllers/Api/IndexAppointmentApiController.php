<?php

namespace App\Modules\Appointment\Controllers\Api;

use App\Common\Controllers\ApiController;

use App\Modules\Appointment\Services\AppointmentService;

class IndexAppointmentApiController extends ApiController
{
    public function __invoke(AppointmentService $service)
    {
        $data = $service->index();

        return [];
    }
}
