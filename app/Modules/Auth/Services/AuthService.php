<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\DTOs\AuthTokenDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\Services\UserService;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Modules\Auth\Exceptions\LoginFailedException;

class AuthService
{
    public function __construct(
        protected readonly UserService $userService
    ) {}

    public function register(CreateUserDTO $createUserDTO): AuthTokenDTO
    {
        $user = $this->userService->create($createUserDTO);
        $token = JWTAuth::fromUser($user);

        return new AuthTokenDTO($token, $user);
    }

    public function login(array $credentials): AuthTokenDTO
    {
        if (!$token = JWTAuth::attempt($credentials) /* !$token = Auth::attempt($credentials) */) {
            throw new LoginFailedException(__('auth.failed'));
        }

        return new AuthTokenDTO($token, Auth::user());
    }

    public function me(): User
    {
        return Auth::user();
    }

    public function refresh(): AuthTokenDTO
    {
        $token = Auth::refresh();

        return new AuthTokenDTO($token, Auth::user());
    }

    public function logout(): void
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        // Auth::logout();
    }
}
