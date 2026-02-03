<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
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
}
