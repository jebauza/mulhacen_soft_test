<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Common\DTOs\PagePaginationDTO;
use App\Common\DTOs\OffsetPaginationDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class UserService
{
    public function __construct(
        protected readonly UserRepository $userRepo
    ) {}

    public function getUsers(string $search = null): Collection
    {
        return $this->userRepo->queryBySearch($search)->get();
    }

    public function paginate(?string $search, int $page = null, int $perPage = null): PagePaginationDTO
    {
        $query = $this->userRepo->queryBySearch($search);
        $total = $query->count();
        $page = $page ?? 1;
        $perPage = $perPage ?? 100;

        $items = $query->forPage($page, $perPage)->get();

        return new PagePaginationDTO(
            $items,
            $total,
            $perPage,
            $page
        );
    }

    public function offsetPaginate(?string $search, int $limit = null, int $offset = null): OffsetPaginationDTO
    {
        $query = $this->userRepo->queryBySearch($search);
        $total = $query->count();
        $limit = $limit ?? 100;
        $offset = $offset ?? 0;

        $items = $query->offset($offset)->limit($limit)->get();

        return new OffsetPaginationDTO(
            $items,
            $total,
            $limit,
            $offset
        );
    }

    public function cursorPaginate(?string $search, int $perPage = null): CursorPaginator
    {
        return $this->userRepo->queryBySearch($search)->cursorPaginate($perPage ?? 100);
    }

    public function findOrFail(string $id): User
    {
        return $this->userRepo->findOrFail($id);
    }

    public function create(CreateUserDTO $dto): User
    {
        return $this->userRepo->create($dto->toArray());
    }

    public function update(string $id, UpdateUserDTO $dto): User
    {
        $user = $this->findOrFail($id);

        return $this->userRepo->update($user, $dto->toArray());;
    }

    public function delete(string $id): void
    {
        $user = $this->findOrFail($id);

        $this->userRepo->delete($user);
    }
}
