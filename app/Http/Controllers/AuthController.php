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

    public function me()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            return response()->json($user);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to fetch user profile'], 500);
        }
    }

    public function refresh()
    {
        $token = Auth::refresh();
        return response()->json($this->getDataToken($token));
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            // Auth::logout();
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
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
