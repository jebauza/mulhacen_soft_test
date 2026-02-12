<?php

namespace App\Modules\User\Requests;

use App\Modules\User\Models\User;
use App\Common\Requests\ApiRequest;
use App\Modules\User\DTOs\CreateUserDTO;

class StoreUserRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            CreateUserDTO::EMAIL => 'required|email|unique:' . User::TABLE . ',' . User::EMAIL,
            CreateUserDTO::PASSWORD => 'required|string|min:8',
        ];
    }
}
