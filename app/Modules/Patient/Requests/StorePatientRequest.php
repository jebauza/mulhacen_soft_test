<?php

namespace App\Modules\Patient\Requests;

use App\Common\Requests\ApiRequest;
use App\Modules\Patient\Models\Patient;
use App\Modules\Patient\DTOs\CreatePatientDTO;

class StorePatientRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            CreatePatientDTO::NAME => 'required|string|max:255',
            CreatePatientDTO::EMAIL => 'required|email|max:255|unique:' . Patient::TABLE . ',' . Patient::EMAIL,
            CreatePatientDTO::PHONE => 'required|string|max:20|unique:' . Patient::TABLE . ',' . Patient::PHONE,

            CreatePatientDTO::NOTES => 'nullable|string',
        ];
    }
}
