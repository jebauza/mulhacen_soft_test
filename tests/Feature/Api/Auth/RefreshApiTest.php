<?php

namespace Tests\Feature\Api\Auth;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RefreshApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/refresh';
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()
            ->withPassword('12345678')
            ->create();
    }

    public function test_refresh_200()
    {
        $token = $this->getAccessToken($this->user);

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
                ],
            ]);
    }

    public function test_refresh_invalid_token_401()
    {
        $this->assertEndpointRequiresAuth('get', $this->api);
    }
}
