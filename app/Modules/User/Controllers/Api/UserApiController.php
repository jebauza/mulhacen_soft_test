<?php

namespace App\Modules\User\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Common\Responses\ApiResponse;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Common\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Modules\User\Services\UserService;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Requests\StoreUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;

class UserApiController extends ApiController
{
    public function __construct(
        protected readonly UserService $service,
    ) {}

    /**
     * Display a listing of the resource.
     */
    /**
     * @LRDparam search nullable|string
     *
     * @lrd:start
     *
     * **Set Global Headers**
     * ```json
     *{"Authorization": "Bearer <access_token>", "Content-Type": "application/json", "Accept": "application/json"}
     * ```
     *
     * **200 OK**
     * ```json
     *{"message":"OK","data":[{"id":"019c3f62-4970-702a-ac25-a1ab6a7a1fca","name":"Alena Weimann","email":"aufderhar.lucienne@example.net"},{"id":"019c3f62-4978-72ae-a860-ac2bf607b2d8","name":"Andre Schowalter DDS","email":"estefania.jerde@example.com"}]}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"search":["The search field must be a string."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|422|500
     */
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
        ]);

        if ($validator->fails())
            return ApiResponse::validation($validator->errors()->toArray());

        return ApiResponse::successData(
            UserResource::collection($this->service->all($request->input('search'))),
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @lrd:start
     *
     * **Set Global Headers**
     * ```json
     *{"Authorization": "Bearer <access_token>", "Content-Type": "application/json", "Accept": "application/json"}
     * ```
     *
     * **201 Created**
     * ```json
     *{"message":"Created","data":{"id":"019c000b-1ba8-734c-87c0-51ad16ba7d68","name":"Test Test","email":"test@test.com"}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"email":["The email field is required."],"name":["The name field is required."],"password":["The password field is required."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 201|401|422|500
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $dto = CreateUserDTO::fromRequest($request);

        $user = DB::transaction(function () use ($dto) {
            return $this->service->create($dto);
        });

        return ApiResponse::created(new UserResource($user));
    }

    /**
     * Display the specified resource.
     */
    /**
     * @lrd:start
     *
     * **Notes**
     * - Requires **Access Token** obtained from **auth/login**, configuration in **auth/me**.
     *
     * **Description**
     * - Retrieve and display a specific user by its ID.
     *
     * **200 OK**
     * ```json
     *{"message":"OK","data":{"id":"019c3f62-4970-702a-ac25-a1ab6a7a1fca","name":"Alena Weimann","email":"aufderhar.lucienne@example.net"}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **404 Not Found**
     * ```json
     *{"message":"Not Found","errors":{"resource":["The requested resource does not exist"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"user":["Must be a valid UUID."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|404|422|500
     */
    public function show(string $id)
    {
        if (!Str::isUuid($id)) {
            return ApiResponse::validation(['user' => [__('Must be a valid UUID.')]]);
        }

        $user = $this->service->findById($id);

        return ApiResponse::successData(
            new UserResource($user)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @lrd:start
     *
     * **Set Global Headers**
     * ```json
     *{"Authorization": "Bearer <access_token>", "Content-Type": "application/json", "Accept": "application/json"}
     * ```
     *
     * **201 Created**
     * ```json
     *{"message":"Created","data":{"id":"019c000b-1ba8-734c-87c0-51ad16ba7d68","name":"Test Test","email":"test@test.com"}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **404 Not Found**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"email":["The email field is required."],"name":["The name field is required."],"password":["The password field is required."],"user":["Must be a valid UUID."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 201|401|404|422|500
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $dto = new UpdateUserDTO(...$request->validated());

        $user = DB::transaction(function () use ($id, $dto) {
            return $this->service->update($id, $dto);
        });

        return ApiResponse::successData(
            new UserResource($user)
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    /**
     * @lrd:start
     *
     * **Notes**
     * - Requires **Access Token** obtained from **auth/login**, configuration in **auth/me**.
     *
     * **Description**
     * - Remove the specified resource from storage.
     *
     * **200 OK**
     * ```json
     *{"message":"Deleted successfully"}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **404 Not Found**
     * ```json
     *{"message":"Not Found","errors":{"resource":["The requested resource does not exist"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"user":["Must be a valid UUID."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|404|422|500
     */
    public function destroy(string $id)
    {
        if (!Str::isUuid($id)) {
            return ApiResponse::validation(['user' => [__('Must be a valid UUID.')]]);
        }

        DB::transaction(function () use ($id) {
            return $this->service->delete($id);
        });

        return ApiResponse::success(__('Deleted successfully'));
    }
}
