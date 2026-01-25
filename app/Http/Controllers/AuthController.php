<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Common\Controllers\ApiController;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;

class AuthController extends ApiController
{
    /**
     * @LRDparam name required|string|max:255
     *
     * @LRDparam email required|string|email|max:255|unique:users
     *
     * @LRDparam password required|string|min:6
     *
     * @lrd:start
     *
     * **201 Created**
     * ```json
     *{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBpL2F1dGgvcmVnaXN0ZXIiLCJpYXQiOjE3NjkzMDQ4MzcsImV4cCI6MTc2OTMwODQzNywibmJmIjoxNzY5MzA0ODM3LCJqdGkiOiIyY0VJZWg5ZDVTY2NJb0FVIiwic3ViIjoiMDE5YmYyYzktNGUyZC03MmM4LWIzYjUtZTY1MzVjYzRjMDQ5IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.1_rlDDk8zGBkniNZLdDH0VLVnd9VrUPx8ycR4OQ_YsE","token_type":"bearer","expires_in":3600,"expires_at":"2026-01-25 02:33:57","user":{"name":"jorge Ernesto","email":"jebauza1989@gmail.com","id":"019bf2c9-4e2d-72c8-b3b5-e6535cc4c049","updated_at":"2026-01-25T01:33:57.000000Z","created_at":"2026-01-25T01:33:57.000000Z"}}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"message":"The name field is required. (and 2 more errors)","errors":{"name":["The name field is required."],"email":["The email has already been taken."],"password":["The password field is required."]}}
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
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);
            DB::commit();

            $data = array_merge($this->getDataToken($token), ['user' => $user]);

            return response()->json($data, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     *
     * @LRDparam email required|string|email
     *
     * @LRDparam password required|string
     *
     * @lrd:start
     *
     * **200 OK**
     * ```json
     *{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBpL2F1dGgvbG9naW4iLCJpYXQiOjE3NjkzMDUwMTYsImV4cCI6MTc2OTMwODYxNiwibmJmIjoxNzY5MzA1MDE2LCJqdGkiOiJFSkQ5bmRHZVVBWUJZQU1zIiwic3ViIjoiMDE5YmYyYzktNGUyZC03MmM4LWIzYjUtZTY1MzVjYzRjMDQ5IiwicHJ2IjoiMjNiZDVjODk0OWY2MDBhZGIzOWU3MDFjNDAwODcyZGI3YTU5NzZmNyJ9.oCXtlw-Q_R2D7ZK-qoKVl0DF1RMpZEmWogKRipDRWB4","token_type":"bearer","expires_in":3600,"expires_at":"2026-01-25 02:36:56"}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"message":"Unauthorized"}
     * ```
     *
     * **422 Unprocessable Entity**
     * ```json
     *{"email":["The email field must be a valid email address."],"password":["The password field is required."]}
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
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials) /* !$token = Auth::attempt($credentials) */) {
                return $this->sendError('Unauthorized', 401);
            }
        } catch (JWTException $e) {
            return $this->sendError500($e->getMessage());
        }

        return response()->json($this->getDataToken($token));
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
     *{"id":"019bf2c9-4e2d-72c8-b3b5-e6535cc4c049","name":"jorge Ernesto","email":"jebauza1989@gmail.com","email_verified_at":null,"created_at":"2026-01-25T01:33:57.000000Z","updated_at":"2026-01-25T01:33:57.000000Z"}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"error":"Unauthorized"}
     * ```
     *
     * **404 Unauthorized**
     * ```json
     *{"error":"User not found"}
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
    public function me()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            return response()->json($user);
        } catch (JWTException $e) {
            return $this->sendError500($e->getMessage());
        }
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
     *{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvYXBpL2F1dGgvcmVmcmVzaCIsImlhdCI6MTc2OTMwNTE1NSwiZXhwIjoxNzY5MzA5MTIzLCJuYmYiOjE3NjkzMDU1MjMsImp0aSI6IkdSaU1tMXJaT2o2NG5zcE0iLCJzdWIiOiIwMTliZjJjOS00ZTJkLTcyYzgtYjNiNS1lNjUzNWNjNGMwNDkiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.E2XjvoOcW_wvDLRs5R4aQcmK4_LUia9hVo3NsSYHlco","token_type":"bearer","expires_in":3600,"expires_at":"2026-01-25 02:45:23"}
     * ```
     *
     * **401 Unauthorized**
     * ```json
     *{"error":"Unauthorized"}
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
        $token = Auth::refresh();
        return response()->json($this->getDataToken($token));
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
     *{"error":"Unauthorized"}
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
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            // Auth::logout();
        } catch (JWTException $e) {
            return $this->sendError500($e->getMessage());
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function getDataToken($token): array
    {
        $expires_in_minutes = Auth::factory()->getTTL(); // auth('api')->factory()->getTTL()

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $expires_in_minutes * 60,
            'expires_at' => now()->addMinutes($expires_in_minutes)->toDateTimeString(),
        ];
    }
}
