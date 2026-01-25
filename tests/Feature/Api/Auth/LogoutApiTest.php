<?php

namespace Tests\Feature\Api\Auth;

use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/logout';

    public function test_logout_200()
    {
        $user = $this->superAdmin();
        $token = $this->getAccessToken($user);

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->postJson($this->api)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->postJson($this->api)
            ->assertStatus(401);
    }

    public function test_refresh_with_invalid_token_401()
    {
        $this->withHeaders(['Authorization' => 'Bearer invalid_token',])
            ->postJson($this->api)
            ->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }
}
