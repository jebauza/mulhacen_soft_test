<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Common\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function baseSearch(?string $search = null, bool|array $relations = false): Builder
    {
        /** @var Builder $builder */
        $builder = User::when(filled($search), function (Builder $q) use ($search) {
            $q->where(User::NAME, 'LIKE', "%{$search}%")
                ->orWhere(User::EMAIL, 'LIKE', "%{$search}%");
        })
            ->when($relations, function (Builder $q) use ($relations) {
                if (is_array($relations)) {
                    $q->with($relations);
                } else {
                    // TODO: Define default relations to load when $relations is true
                }
            })
            ->orderBy(User::TABLE . '.' . User::NAME)
            ->orderBy(User::TABLE . '.' . User::ID);

        return $builder;
    }

    public function search(?string $search, bool|array $relations = false): Collection
    {
        return $this->baseSearch($search, $relations)->get();
    }

    public function searchCount(?string $search): int
    {
        return $this->baseSearch($search)->count();
    }

    public function findOneBy(string $column, string $value): User
    {
        return User::firstWhere($column, $value);
    }
}
