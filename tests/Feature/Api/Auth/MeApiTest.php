<?php

namespace Tests\Feature\Api\Auth;

use App\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/me';

    public function test_me_200()
    {
        $user = $this->superAdmin();
        $token = $this->getAccessToken($user);

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson($this->api)
            ->assertOk()
            ->assertJson([
                'id' => $user->{User::ID},
                'name' => $user->{User::NAME},
                'email' => $user->{User::EMAIL},
                'email_verified_at' => $user->{User::EMAIL_VERIFIED_AT}->toJson(),
                'created_at' => $user->{User::CREATED_AT}->toJson(),
                'updated_at' => $user->{User::UPDATED_AT}->toJson(),
            ]);
    }

    public function test_me_with_invalid_token_401()
    {
        $this->withHeaders(['Authorization' => 'Bearer invalid_token',])
            ->getJson($this->api)
            ->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }
}
