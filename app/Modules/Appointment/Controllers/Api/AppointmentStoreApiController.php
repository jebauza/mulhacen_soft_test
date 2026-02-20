<?php

namespace App\Modules\Appointment\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Common\Responses\ApiResponse;
use App\Common\Controllers\ApiController;
use App\Modules\Appointment\DTOs\CreateAppointmentDTO;
use App\Modules\Appointment\Services\AppointmentService;
use App\Modules\Appointment\Resources\AppointmentResource;
use App\Modules\Appointment\Requests\StoreAppointmentRequest;

class AppointmentStoreApiController extends ApiController
{
    /**
     * @lrd:start
     *
     * **Notes**
     * - Requires **Access Token** obtained from **auth/login**, configuration in **auth/me**.
     *
     * **Description**
     * - Create a new appointment with the provided attributes.
     *
     * **201 Created**
     * ```json
     *{"message":"Created","data":{"id":"019c540e-9c03-73ac-8ad9-1510ca7255b7","patient_id":"019c5401-51f8-726f-ab90-a4e8f35e3499","dentist_id":"019c5401-51ef-71a8-a271-cdbd48480f39","start":"2026-01-12 08:30:00","end":"2026-01-12 08:45:00","duration":15,"reason":"porque quiero","treatment_ids":["b73b2f06-b28a-4b07-88ba-ff68b4286033"]}}
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
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"patient_id":["The patient id field is required."],"dentist_id":["The dentist id field is required."],"start":["The start field is required."],"end":["The end field is required."],"duration":["The duration field is required."],"treatment_ids":["The treatment ids field must be present."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|403|422|500
     */
    public function __invoke(StoreAppointmentRequest $request, AppointmentService $service): JsonResponse
    {
        $dto = CreateAppointmentDTO::fromRequest($request);

        $appointment = DB::transaction(function () use ($service, $dto) {
            return $service->create($dto);
        });

        return ApiResponse::created(new AppointmentResource($appointment));
    }
}
