<?php

namespace App\Modules\Auth\Controllers\Api;

use App\Modules\User\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Common\Responses\ApiResponse;
use App\Modules\Auth\DTOs\AuthTokenDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Common\Controllers\ApiController;
use App\Modules\Auth\Services\AuthService;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Requests\RegisterRequest;

class AuthApiController extends ApiController
{
    public function __construct(
        protected readonly AuthService $authService,
    ) {}

    /**
     * @lrd:start
     *
     * **201 Created**
     * ```json
     *{"message":"User registered successfully","data":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBpL2F1dGgvcmVnaXN0ZXIiLCJpYXQiOjE3NjkzNjQyODcsImV4cCI6MTc2OTM2Nzg4NywibmJmIjoxNzY5MzY0Mjg3LCJqdGkiOiJZY3F5SUVVRlQ1emc3UXpoIiwic3ViIjoiMDE5YmY2NTQtNzFiOC03MzdjLThlN2UtOTFjZmQxODJlNTVlIiwicHJ2IjoiNGE2ZTI1MmQ0OWNjMzVmOWE2ZDI4OTdmZGU0ZjkzMTQ2ZTdjODAyYyJ9.32ID2lVhf-gzKpHnPYXBCCeHdaN0oLDWMiX2oPL_bsE","token_type":"bearer","expires_in":3600,"expires_at":"2026-01-25 19:04:47","user":{"id":"019bf654-71b8-737c-8e7e-91cfd182e55e","name":"jorge Ernesto","email":"jebauza1989@gmail.com"}}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"email":["The email field is required."],"name":["The name field is required."]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 201|422|500
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = CreateUserDTO::fromRequest($request);

        DB::beginTransaction();
        $authDTO = $this->authService->register($dto);
        DB::commit();

        return ApiResponse::created(
            $this->buildTokenResponse($authDTO),
            __('User registered successfully')
        );
    }

    /**
     *
     * @lrd:start
     *
     * **200 OK**
     * ```json
     *{"message":"Login successful","data":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3NjkzNzQxMjksImV4cCI6MTc2OTM3NzcyOSwibmJmIjoxNzY5Mzc0MTI5LCJqdGkiOiJNMHd6amRQcTQ4UDVyYUdhIiwic3ViIjoiMDE5YmY2OWItMWQ5MS03MmIxLTljN2YtMDJlODJhMDU4YzExIiwicHJ2IjoiNGE2ZTI1MmQ0OWNjMzVmOWE2ZDI4OTdmZGU0ZjkzMTQ2ZTdjODAyYyJ9.i2cBo-byKPNMOXAdXZ61afjShaaKSHBxOAizMEAF1-k","token_type":"bearer","expires_in":3600,"expires_at":"2026-01-25 21:48:49"}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"credentials":["These credentials do not match our records."]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"email":["The email field is required."],"password":["The password field is required."]}}
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
    public function login(LoginRequest $request): JsonResponse
    {
        $authDTO = $this->authService->login($request->validated());

        return ApiResponse::success(
            __('Login successful'),
            $this->buildTokenResponse($authDTO)
        );
    }

    /**
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
     *{"message":"Ok","data":{"id":"019bf71d-df84-71da-b9b5-4c3806db1ef7","name":"jorge Ernesto","email":"jebauza1989@gmail.com"}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **404 Not Found**
     * ```json
     *{"message":"Not Found"}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|404|500
     */
    public function me(): JsonResponse
    {
        $user = $this->authService->me();

        return ApiResponse::successData(
            $user->only(User::ID, User::NAME, User::EMAIL)
        );
    }

    /**
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
     *{"message":"OK","data":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBpL2F1dGgvcmVmcmVzaCIsImlhdCI6MTc2OTM4MTM4NSwiZXhwIjoxNzY5Mzg1ODA2LCJuYmYiOjE3NjkzODIyMDYsImp0aSI6Ik1NMFpsT09MUTFLZU9GeEciLCJzdWIiOiIwMTliZjc1OC0xYTM4LTczODEtYWUxYi0xYjg0ZmVmNjdmYTciLCJwcnYiOiI0YTZlMjUyZDQ5Y2MzNWY5YTZkMjg5N2ZkZTRmOTMxNDZlN2M4MDJjIn0.RX0EcotXN0A_ze3UWCPBqPdg8genF2WPbUDa6kb_H6w","token_type":"bearer","expires_in":3600,"expires_at":"2026-01-26 00:03:26"}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|500
     */
    public function refresh()
    {
        $authDTO = $this->authService->refresh();

        return ApiResponse::successData(
            $this->buildTokenResponse($authDTO)
        );
    }

    /**
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
     *{"message":"Successfully logged out"}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **500 Internal Server Error**
     * ```json
     *{"message":"Internal Server Error"}
     * ```
     *
     * @lrd:end
     *
     * @LRDresponses 200|401|500
     */
    public function logout()
    {
        $this->authService->logout();

        return ApiResponse::success(
            __('Successfully logged out')
        );
    }

    protected function buildTokenResponse(AuthTokenDTO $dto): array
    {
        $ttl = Auth::factory()->getTTL(); // auth('api')->factory()->getTTL()

        return [
            'access_token' => $dto->token,
            'token_type' => 'bearer',
            'expires_in' => $ttl * 60,
            'expires_at' => now()->addMinutes($ttl)->toDateTimeString(),
            'user' => $dto->user->only(User::ID, User::NAME, User::EMAIL),
        ];
    }
}
