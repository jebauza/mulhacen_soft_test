<?php

namespace Tests\Feature\Api\Auth;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/logout';
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()
            ->withPassword('12345678')
            ->create();
    }

    public function test_logout_200()
    {
        $token = $this->getAccessToken($this->user);

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

    public function test_refresh_invalid_token_401()
    {
        $this->assertEndpointRequiresAuth('post', $this->api);
    }
}
