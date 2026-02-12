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
     * - Create a new user with the provided attributes.
     *
     * **201 Created**
     * ```json
     *{"message":"Created","data":{"id":"a106317e-889c-40f6-a8dd-cdcffb2b9886","name":"Test8","surname":"Test8","email":"test7@test.com","avatar":"http:\/\/localhost:8080\/storage\/user\/avatars\/P1NohQnOiGctG5beFqp2PIAzuWtNLPBLm1xBZBIq.jpg","phone":"622788616","type_document":"dni","n_document":"jmYYDaHRwh","birth_date":"1989-12-19","designation":"sdsdsdsdsd","gender":"female","roles":[{"id":"a1030860-2a5d-482d-b4d2-8450ea436186","name":"rol test2"}],"all_permissions":[{"id":"a103085f-d38c-4c86-a46a-79b1e5c3c419","name":"veterinary.register"}]}}
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
     *{"message":"Validation errors","errors":{"email":["The email field is required."],"name":["The name field is required."],"surname":["The surname field is required."],"password":["The password field is required."]}}
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
