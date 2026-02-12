<?php

namespace App\Modules\Patient\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Common\Responses\ApiResponse;
use App\Common\Controllers\ApiController;
use App\Modules\Patient\DTOs\CreatePatientDTO;
use App\Modules\Patient\Services\PatientService;
use App\Modules\Patient\Resources\PatientResource;
use App\Modules\Patient\Requests\StorePatientRequest;

class StorePatientApiController extends ApiController
{
    /**
     * @lrd:start
     *
     * **Notes**
     * - Requires **Access Token** obtained from **auth/login**, configuration in **auth/me**.
     *
     * **Description**
     * - Create a new patient with the provided attributes.
     *
     * **201 Created**
     * ```json
     *{"message":"Created","data":{"id":"019c5413-829a-733c-bcf1-340c646c04f3","name":"Test Test","email":"test@test.com","phone":"622783646","notes":"prueba"}}
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
     *{"message":"Validation errors","errors":{"name":["The name field is required."],"email":["The email field is required."],"phone":["The phone field is required."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 201|401|403|422|500
     */
    public function __invoke(StorePatientRequest $request, PatientService $service): JsonResponse
    {
        $dto = CreatePatientDTO::fromRequest($request);

        $patient = DB::transaction(function () use ($service, $dto) {
            return $service->create($dto);
        });

        return ApiResponse::created(new PatientResource($patient));
    }
}
