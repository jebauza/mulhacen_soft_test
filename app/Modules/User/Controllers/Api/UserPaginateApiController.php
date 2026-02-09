<?php

namespace App\Modules\User\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Common\Responses\ApiResponse;
use App\Common\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Modules\User\Services\UserService;
use App\Modules\User\Resources\UserResource;

class UserPaginateApiController extends ApiController
{
    public function __construct(
        protected readonly UserService $service
    ) {}

    /**
     * Paginate and retrieve a list of users.
     *
     * Validates pagination parameters, delegates to `userService`, and returns a paginated
     * collection of `UserResource` objects via an `ApiResponse`.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing 'search', 'page', and 'per_page' parameters.
     * @return \Illuminate\Http\JsonResponse Returns a paginated list of users (200) or validation errors (422).
     */
    /**
     * @LRDparam search nullable|string
     * @LRDparam page nullable|integer|min:1
     * @LRDparam per_page nullable|integer|min:1|max:100
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
     *{"message":"OK","data":[{"id":"019c3f62-4970-702a-ac25-a1ab6a7a1fca","name":"Alena Weimann","email":"aufderhar.lucienne@example.net"},{"id":"019c3f62-4978-72ae-a860-ac2bf607b2d8","name":"Andre Schowalter DDS","email":"estefania.jerde@example.com"}],"meta":{"current_page":1,"per_page":2,"last_page":11,"total":22}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"page":["The page field must be at least 1."]}}
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
    public function paginate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
            'page'  => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails())
            return ApiResponse::validation($validator->errors()->toArray());

        $pagePaginationDTO = $this->service->pagePaginate(
            $request->input('search'),
            $request->input('page'),
            $request->input('per_page')
        );

        return ApiResponse::successData(
            UserResource::collection($pagePaginationDTO->items),
            200,
            $this->getMetaPagination($pagePaginationDTO)
        );
    }

    /**
     * @LRDparam search nullable|string
     * @LRDparam offset nullable|integer|min:0
     * @LRDparam limit nullable|integer|min:1|max:100
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
     *{"message":"OK","data":[{"id":"019c3f62-4970-702a-ac25-a1ab6a7a1fca","name":"Alena Weimann","email":"aufderhar.lucienne@example.net"},{"id":"019c3f62-4978-72ae-a860-ac2bf607b2d8","name":"Andre Schowalter DDS","email":"estefania.jerde@example.com"}],"meta":{"offset":0,"limit":2,"total":22}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"limit":["The limit field must be at least 1."]}}
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
    public function offsetPaginate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
            'offset' => 'nullable|integer|min:0',
            'limit'  => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails())
            return ApiResponse::validation($validator->errors()->toArray());

        $offsetPaginationDTO = $this->service->offsetPaginate(
            $request->input('search'),
            $request->input('offset'),
            $request->input('limit'),
        );

        return ApiResponse::successData(
            UserResource::collection($offsetPaginationDTO->items),
            200,
            $this->getMetaPagination($offsetPaginationDTO)
        );
    }

    /**
     * @LRDparam search nullable|string
     * @LRDparam cursor nullable|string
     * @LRDparam per_page nullable|integer|min:1|max:100
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
     *{"message":"OK","data":[{"id":"019c3f62-4970-702a-ac25-a1ab6a7a1fca","name":"Alena Weimann","email":"aufderhar.lucienne@example.net"},{"id":"019c3f62-4978-72ae-a860-ac2bf607b2d8","name":"Andre Schowalter DDS","email":"estefania.jerde@example.com"}],"meta":{"per_page":2,"next_cursor":"eyJ1c2Vycy5uYW1lIjoiQW5kcmUgU2Nob3dhbHRlciBERFMiLCJ1c2Vycy5pZCI6IjAxOWMzZjYyLTQ5NzgtNzJhZS1hODYwLWFjMmJmNjA3YjJkOCIsIl9wb2ludHNUb05leHRJdGVtcyI6dHJ1ZX0","prev_cursor":null}}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized","errors":{"auth":["Authentication token is invalid or expired"]}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"Validation errors","errors":{"per_page":["The per page field must be at least 1."]}}
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
    public function cursorPaginate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
            'cursor' => 'nullable|string',
            'per_page'  => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails())
            return ApiResponse::validation($validator->errors()->toArray());

        $cursorPaginate = $this->service->cursorPaginate(
            $request->input('search'),
            $request->input('per_page')
        );

        return ApiResponse::successData(
            UserResource::collection($cursorPaginate->items()),
            200,
            $this->getMetaPagination($cursorPaginate)
        );
    }
}
