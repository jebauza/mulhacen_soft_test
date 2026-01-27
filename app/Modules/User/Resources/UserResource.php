<?php

namespace App\Modules\User\Resources;

use Illuminate\Http\Request;
use App\Modules\User\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->{User::ID},
            'name' => $this->{User::NAME},
            'email' => $this->{User::EMAIL},
        ];
    }
}
