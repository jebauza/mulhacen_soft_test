<?php

namespace Tests\Feature\Api\Auth;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthRefreshApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/refresh';

    public function test_refresh_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(self::GET, $this->api);
    }

    public function test_refresh_200()
    {
        $user = User::factory()->create();
        $token = $this->getAccessToken($user);

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson($this->api)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'expires_at',
                    'user' => [
                        'id',
                        'name',
                        'email',
                    ]
                ],
            ])
            ->assertJsonPath('message', __('OK'))
            ->assertJsonPath('data.user', [
                'id' => $user->{User::ID},
                'name' => $user->{User::NAME},
                'email' => $user->{User::EMAIL},
            ]);
    }
}
