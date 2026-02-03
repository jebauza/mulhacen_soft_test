<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Support\Str;
use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/login';
    private array $payload = [];

    protected function setUp(): void
    {
        parent::setUp();
        $users = User::factory(2)
            ->withPassword('12345678')
            ->create();

        $this->payload = [
            'email' => $users->random()->{User::EMAIL},
            'password' => '12345678',
        ];
    }

    public function test_login_200(): void
    {
        $this->postJson($this->api, $this->payload)
            ->assertStatus(200)
            ->assertJsonPath('message', __('Login successful'))
            ->assertJsonStructure([
                'message',
                'data' => [
                    'access_token',
                    'token_type',
                    'expires_in',
                    'expires_at',
                    'user'
                ]
            ]);
    }

    public function test_login_unauthorized_401(): void
    {
        $this->postJson($this->api, [
            'email' => $this->payload['email'],
            'password' => 'wrongpass',
        ])
            ->assertStatus(401)
            ->assertJson([
                'message' => __('Unauthorized'),
                'errors' => [
                    'credentials' => [__('auth.failed')]
                ]
            ]);
    }

    public function test_login_validation_422(): void
    {
        // Data required
        $this->postJson($this->api, [])
            ->assertStatus(422)
            ->assertJsonPath('message', __('Validation errors'))
            ->assertJsonStructure([
                'message',
                'errors' => ['email', 'password'],
            ]);

        // Data string
        $this->postJson($this->api, [
            'email' => 4,
            'password' => 1000000,
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email', 'password'],
            ]);

        // Data min
        $data = $this->payload;
        $data['password'] = Str::random(7);
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password'],
            ]);

        // Invalid email
        $data = $this->payload;
        $data['email'] = 'invalid_email';
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }
}
