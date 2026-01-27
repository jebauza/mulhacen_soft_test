<?php

namespace App\Modules\User\Controllers\Api;

use Illuminate\Http\Request;
use App\Common\Responses\ApiResponse;
use App\Common\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;
use App\Modules\User\Services\UserService;
use App\Modules\User\Resources\UserResource;

class UserApiController extends ApiController
{
    public function __construct(
        protected readonly UserService $userService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        dd();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd('store');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        dd('show');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        dd('update');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        dd('update');
    }

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
     *
     * @LRDparam page nullable|integer|min:1
     *
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
     *{"message":"OK","data":[{"id":"019bf751-c10f-70c4-a94c-4a48a1ff138a","name":"Cortez Fisher V","email":"aschulist@example.org"},{"id":"019bf751-c11c-72c0-a658-c13e42d9da45","name":"Dr. Hester Fahey Sr.","email":"abbey.schaefer@example.net"},{"id":"019bf751-c115-7205-8494-daf948ab6e31","name":"Mrs. Isabell Stehr PhD","email":"marco20@example.net"},{"id":"019bf751-c11e-7358-af96-3ecb2754ec9b","name":"Stephania Stark","email":"twuckert@example.com"}],"meta":{"per_page":10,"current_page":1,"last_page":1,"total":4}}
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
    public function paginate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
            'page'  => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        if ($validator->fails())
            return ApiResponse::validation($validator->errors()->toArray());

        $pagePaginationDTO = $this->userService->paginate(
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
     * Handles offset-based user pagination.
     *
     * Validates optional 'search' (string), 'limit' (int:1-100), and 'offset' (int:0+) from the request.
     * Returns validation errors on failure, otherwise fetches and returns paginated users
     * as `UserResource` collection via `ApiResponse::successData` with metadata.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * @LRDparam search nullable|string
     *
     * @LRDparam limit nullable|integer|min:1|max:100
     *
     * @LRDparam offset nullable|integer|min:0
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
     *{"message":"OK","data":[{"id":"019bf751-c11d-73e6-b8f2-bc3d75042475","name":"Drew Kassulke","email":"oceane86@example.org"},{"id":"019bf751-c114-71c6-ba48-0f70f8922d14","name":"Emile Wunsch V","email":"bessie95@example.com"},{"id":"019bf751-c11e-7358-af96-3ecb27455e2a","name":"Gerda Schaden","email":"eladio.collier@example.org"},{"id":"019bf751-c111-7097-95a2-646053f20147","name":"Isaac Kuhlman Sr.","email":"mjohnston@example.com"},{"id":"019bf751-c11b-73ac-8995-8f17b819f1e8","name":"Jerad Durgan","email":"rmedhurst@example.org"}],"meta":{"limit":5,"total":22,"offset":5}}
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
    public function offsetPaginate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
            'limit'  => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails())
            return ApiResponse::validation($validator->errors()->toArray());

        $offsetPaginationDTO = $this->userService->offsetPaginate(
            $request->input('search'),
            $request->input('limit'),
            $request->input('offset')
        );

        return ApiResponse::successData(
            UserResource::collection($offsetPaginationDTO->items),
            200,
            $this->getMetaPagination($offsetPaginationDTO)
        );
    }

    /**
     * Handles cursor-based pagination for users, validating input and returning paginated user data or errors.
     *
     * @param \Illuminate\Http\Request $request The incoming HTTP request with pagination parameters.
     * @return \Illuminate\Http\JsonResponse JSON response with paginated user data or validation errors.
     */
    /**
     * @LRDparam search nullable|string
     *
     * @LRDparam per_page nullable|integer|min:1|max:100
     *
     * @LRDparam cursor nullable|string
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
     *{"message":"OK","data":[{"id":"019bf751-c10f-70c4-a94c-4a48a1ff138a","name":"Cortez Fisher V","email":"aschulist@example.org"},{"id":"019bf751-c11c-72c0-a658-c13e42d9da45","name":"Dr. Hester Fahey Sr.","email":"abbey.schaefer@example.net"},{"id":"019bf751-c115-7205-8494-daf948ab6e31","name":"Mrs. Isabell Stehr PhD","email":"marco20@example.net"},{"id":"019bf751-c11e-7358-af96-3ecb2754ec9b","name":"Stephania Stark","email":"twuckert@example.com"}],"meta":{"per_page":10,"next_cursor":null,"prev_cursor":null}}
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
    public function cursorPaginate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string',
            'per_page'  => 'nullable|integer|min:1|max:100',
            'cursor' => 'nullable|string',
        ]);

        if ($validator->fails())
            return ApiResponse::validation($validator->errors()->toArray());

        $cursorPaginate = $this->userService->cursorPaginate(
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
