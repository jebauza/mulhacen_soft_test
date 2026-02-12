<?php

namespace App\Modules\User\Requests;

use App\Modules\User\Models\User;
use App\Common\Requests\ApiRequest;
use App\Modules\User\DTOs\UpdateUserDTO;

class UpdateUserRequest extends ApiRequest
{
    private ?string $userId = null;

    public function rules(): array
    {
        $this->userId = $this->route('user');

        return [
            UpdateUserDTO::EMAIL => 'required|email|unique:' . User::TABLE . ',' . User::EMAIL . ',' . $this->userId,
            UpdateUserDTO::PASSWORD => 'required|string|min:8',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateUuidParam('user', $validator);
        });
    }
}
