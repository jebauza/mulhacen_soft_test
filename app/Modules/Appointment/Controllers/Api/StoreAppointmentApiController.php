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

class StoreAppointmentApiController extends ApiController
{
    public function __invoke(StoreAppointmentRequest $request, AppointmentService $service): JsonResponse
    {
        $dto = CreateAppointmentDTO::fromRequest($request);

        $appointment = DB::transaction(function () use ($service, $dto) {
            return $service->create($dto);
        });

        return ApiResponse::created(new AppointmentResource($appointment));
    }
}
