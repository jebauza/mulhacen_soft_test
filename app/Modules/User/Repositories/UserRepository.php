<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Common\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function update(string $id, array $updateUserDTO): User
    {
        $user = User::updateOrCreate(
            [
                User::ID => $id
            ],
            [
                User::NAME => $updateUserDTO[UpdateUserDTO::NAME],
                User::EMAIL => $updateUserDTO[UpdateUserDTO::EMAIL],
                User::PASSWORD => $updateUserDTO[UpdateUserDTO::PASSWORD],
            ]
        );

        return $user;
    }
}
