<?php

namespace App\Modules\Patient\Resources;

use Illuminate\Http\Request;
use App\Modules\Patient\Models\Patient;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->{Patient::ID},
            'name' => $this->{Patient::NAME},
            'email' => $this->{Patient::EMAIL},
            'phone' => $this->{Patient::PHONE},
            'notes' => $this->{Patient::NOTES},
        ];
    }
}
