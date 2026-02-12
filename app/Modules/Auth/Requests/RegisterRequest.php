<?php

namespace App\Modules\Auth\Requests;

use App\Modules\User\Models\User;
use App\Common\Requests\ApiRequest;
use App\Modules\User\DTOs\CreateUserDTO;

class RegisterRequest extends ApiRequest
{

    public function rules(): array
    {
        return [
            CreateUserDTO::EMAIL => 'required|email|unique:' . User::TABLE . ',' . User::EMAIL,
            CreateUserDTO::PASSWORD => 'required|string|min:8'
        ];
    }
}
