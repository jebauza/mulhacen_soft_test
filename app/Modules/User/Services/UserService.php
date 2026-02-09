<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Common\DTOs\OffsetPaginationDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\DTOs\UpdateUserDTO;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class UserService
{
    public function __construct(
        protected readonly UserRepository $userRepo
    ) {}

    public function all(string $search = null)
    {
        return $this->userRepo->search($search, true);
    }

    public function pagePaginate(?string $search = null, int $page = null, int $perPage = null)
    {
        $page = $page ?? 1;
        $perPage = $perPage ?? 100;

        return $this->userRepo->pagination(
            $this->userRepo->baseSearch($search, true),
            $page,
            $perPage,
        );
    }

    public function offsetPaginate(?string $search, int $offset = null, int $limit = null): OffsetPaginationDTO
    {
        $offset = $offset ?? 0;
        $limit = $limit ?? 100;

        return $this->userRepo->offsetPagination(
            $this->userRepo->baseSearch($search, true),
            $offset,
            $limit
        );
    }

    public function cursorPaginate(?string $search, int $perPage = null): CursorPaginator
    {
        $perPage = $perPage ?? 100;

        return $this->userRepo->cursorPagination(
            $this->userRepo->baseSearch($search, true),
            $perPage
        );
    }

    public function findById(string $id): User
    {
        /** @var User $user */
        $user = $this->userRepo->findOrFail($id);

        return $user;
    }

    public function create(CreateUserDTO $dto): User
    {
        return $this->userRepo->create($dto->toArray());
    }

    public function update(string $id, UpdateUserDTO $dto): User
    {
        $user = $this->findById($id);

        return $this->userRepo->update($user, $dto->toArray());;
    }

    public function delete(string $id): void
    {
        $user = $this->findById($id);

        $this->userRepo->delete($user);
    }
}
