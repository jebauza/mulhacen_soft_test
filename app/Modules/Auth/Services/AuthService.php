<?php

namespace App\Modules\Auth\Services;

use App\Modules\User\Models\User;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\Services\UserService;

class AuthService
{
    public function __construct(
        protected readonly UserService $userService
    ) {}

    public function register(CreateUserDTO $createUserDTO): User
    {
        $user = $this->userService->createUser($createUserDTO);

        return $user;
    }

    // public function me(): array
    // {
    //     $user = Auth::user();
    //     $data = $user->only(
    //         User::ID,
    //         User::EMAIL,
    //         User::NAME,
    //         User::SURNAME,
    //         User::AVATAR,
    //     );
    //     $data['permissions'] = $this->userRepo->getAllPermissions($user)->pluck(Permission::NAME);
    //     $data['roles'] = $this->userRepo->getRoles($user)->pluck(Role::NAME);

    //     return $data;
    // }
}
