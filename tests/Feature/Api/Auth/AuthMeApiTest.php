<?php

namespace Tests\Feature\Api\Auth;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthMeApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/me';

    public function test_me_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(self::GET, $this->api);
    }

    public function test_me_200()
    {
        $user = User::factory()->create();
        $token = $this->getAccessToken($user);

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson($this->api)
            ->assertOk()
            ->assertJson([
                'message' => __('OK'),
                'data' => [
                    'id' => $user->{User::ID},
                    'email' => $user->{User::EMAIL},
                ],
            ]);
    }
}
