<?php

namespace App\Modules\Appointment\Controllers\Api;

use App\Common\Controllers\ApiController;

use App\Common\Responses\ApiResponse;
use App\Modules\Appointment\Resources\DateScheduleResource;
use App\Modules\Appointment\Services\AppointmentService;

class AppointmentScheduleApiController extends ApiController
{
    /**
     * @lrd:start
     *
     * **Notes**
     * - Requires **Access Token** obtained from **auth/login**, configuration in **auth/me**.
     *
     * **Description**
     * - Returns the appointment schedule grouped by date. Each day lists appointments with id, patient_id, dentist_id, start, end, duration, reason, and treatment_ids.
     *
     * **200 OK**
     * ```json
     *{"message":"OK","data":[{"date":"2026-01-12","appointments":[{"id":"019c540c-fa9b-72a2-99f5-a7f7092ecb50","patient_id":"019c5401-51f8-726f-ab90-a4e8f35e3499","dentist_id":"019c5401-51ef-71a8-a271-cdbd48480f39","start":"2026-01-12 08:00:00","end":"2026-01-12 08:15:00","duration":15,"reason":"porque quiero","treatment_ids":["b73b2f06-b28a-4b07-88ba-ff68b4286033","d73211db-eb64-4199-9845-42ae696ba104"]},{"id":"019c540e-9c03-73ac-8ad9-1510ca7255b7","patient_id":"019c5401-51f8-726f-ab90-a4e8f35e3499","dentist_id":"019c5401-51ef-71a8-a271-cdbd48480f39","start":"2026-01-12 08:30:00","end":"2026-01-12 08:45:00","duration":15,"reason":"porque quiero","treatment_ids":["b73b2f06-b28a-4b07-88ba-ff68b4286033"]}]},{"date":"2026-02-12","appointments":[{"id":"019c5401-5231-73a9-a5d8-5185b06a0969","patient_id":"019c5401-51f9-7081-b3e5-c2b015bc7a5c","dentist_id":"019c5401-5046-70d0-a8a4-d282cfe9eb65","start":"2026-02-12 14:30:00","end":"2026-02-12 15:15:00","duration":45,"reason":null,"treatment_ids":["6e6ed772-9825-4c9c-bb49-664439903152","89252554-4716-4782-b874-c1f61c1e983c"]}]}]}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **403 Forbidden**
     * ```json
     *{"message":"You do not have permission to access this resource"}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|403|500
     */
    public function __invoke(AppointmentService $service)
    {
        return ApiResponse::successData(DateScheduleResource::collection($service->schedule()));
    }
}
