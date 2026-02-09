<?php

namespace Tests\Feature\Api\Auth;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthLogoutApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/logout';

    public function test_logout_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(self::POST, $this->api);
    }

    public function test_logout_200()
    {
        $token = $this->getAccessToken(User::factory()->create());

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->postJson($this->api)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);

        // JWTAuth::unsetToken();
        // Auth::forgetGuards();

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->postJson($this->api)
            ->assertStatus(401);
    }
}
