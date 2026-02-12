<?php

namespace App\Common\Requests;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{
    public function validateUuidParam(string $paramName, $validator,): void
    {
        $uuid = $this->route($paramName);

        if (!Str::isUuid($uuid)) {
            $validator->errors()->add(
                $paramName,
                __('Must be a valid UUID.')
            );
        }
    }
}
