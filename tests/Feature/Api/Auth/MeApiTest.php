<?php

namespace Tests\Feature\Api\Auth;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/me';
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()
            ->withPassword('12345678')
            ->create();
    }

    public function test_me_200()
    {
        $token = $this->getAccessToken($this->user);

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson($this->api)
            ->assertOk()
            ->assertJson([
                'message' => __('OK'),
                'data' => [
                    'id' => $this->user->{User::ID},
                    'name' => $this->user->{User::NAME},
                    'email' => $this->user->{User::EMAIL},
                ],
            ]);
    }

    public function test_me_invalid_token_401()
    {
        $this->assertEndpointRequiresAuth('get', $this->api);
    }
}
