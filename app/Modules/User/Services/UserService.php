<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Common\DTOs\PagePaginationDTO;
use App\Common\DTOs\OffsetPaginationDTO;
use App\Modules\User\DTOs\CreateUserDTO;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\CursorPaginator;

class UserService
{
    public function __construct(
        protected readonly UserRepository $userRepo
    ) {}

    public function cursorPaginate(?string $search, int $perPage = null): CursorPaginator
    {
        return $this->userRepo->queryBySearch($search)->cursorPaginate($perPage ?? 100);
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

    /* public function getUserById(string $id): User
    {
        return $this->userRepo->findOrFail($id, true);
    } */

    public function createUser(CreateUserDTO $createUserDTO): User
    {
        return $this->userRepo->create($createUserDTO->toArray());
    }

    /* public function updateUser(string $id, UpdateUserDTO $updateUserDTO, ?UploadedFile $avatar): User
    {
        $updateUserDTO->{UpdateUserDTO::PASSWORD} = Hash::make($updateUserDTO->{UpdateUserDTO::PASSWORD});
        $oldAvatar = null;

        if ($avatar) {
            $updateUserDTO->{UpdateUserDTO::AVATAR} = FileHelper::saveFile(
                $avatar,
                User::PATH_FOLDER_AVATARS,
                'public'
            );

            $oldAvatar = $this->userRepo->findOrFail($id)->{User::AVATAR};
        }

        try {
            $user = $this->userRepo->update($id, $updateUserDTO->toArray(true));

            if ($updateUserDTO->{UpdateUserDTO::ROLE_ID}) {
                $user = $this->userRepo->syncRoles($user, [$updateUserDTO->{CreateUserDTO::ROLE_ID}]);
            }

            if ($oldAvatar) {
                FileHelper::deleteFile($oldAvatar, 'public');
            }

            return $this->userRepo->loadRelations($user, false, true);
        } catch (\Throwable $th) {
            if ($updateUserDTO->{UpdateUserDTO::AVATAR}) {
                FileHelper::deleteFile($updateUserDTO->{UpdateUserDTO::AVATAR}, 'public');
            }
            throw $th;
        }
    } */

    /* public function deleteUser(string $id)
    {
        $user = $this->userRepo->findOrFail($id);

        $this->userRepo->delete($user->{User::ID});

        if ($user->{User::AVATAR}) {
            FileHelper::deleteFile($user->{User::AVATAR}, 'public');
        }
    } */
}
