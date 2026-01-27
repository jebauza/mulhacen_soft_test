<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use App\Modules\User\DTOs\UpdateUserDTO;
use Illuminate\Database\Eloquent\Builder;
use App\Common\Repositories\BaseRepository;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function queryBySearch(?string $search, bool $withRelations = false): Builder
    {
        return User::when(filled($search), function (Builder $q) use ($search) {
            $q->where(User::NAME, 'LIKE', "%{$search}%")
                ->orWhere(User::EMAIL, 'LIKE', "%{$search}%");
        })
            // ->when($withRelations, function (Builder $q) {
            //     $q->with([
            //         'permissions:id,name',
            //         'roles.permissions:id,name'
            //     ]);
            // })
            ->orderBy(User::TABLE . '.' . User::NAME)
            ->orderBy(User::TABLE . '.' . User::ID);
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
