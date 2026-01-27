<?php

namespace Tests\Feature\Api\Auth;

use Illuminate\Support\Str;
use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/register';
    private array $payload = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->payload = [
            'name' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'test123456789',
        ];
    }

    public function test_register_201(): void
    {
        $this->postJson($this->api, $this->payload)
            ->assertStatus(201)
            ->assertJsonPath('message', __('User registered successfully'))
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
                ]
            ]);

        // Database verification
        $this->assertDatabaseHas(User::TABLE, [
            User::EMAIL => $this->payload['email'],
        ]);

        // Verify that the password field was not saved as plain text
        $this->assertDatabaseMissing(User::TABLE, [
            User::EMAIL => $this->payload['email'],
            User::PASSWORD => $this->payload['password'],
        ]);
    }

    public function test_register_validation_422(): void
    {
        // Data required
        $this->postJson($this->api, [])
            ->assertStatus(422)
            ->assertJsonPath('message', __('Validation errors'))
            ->assertJsonStructure([
                'message',
                'errors' => ['email', 'name', 'password'],
            ]);

        // Data string
        $data = $this->payload;
        $data['name'] = 4;
        $data['password'] = 10000000;
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name', 'password'],
            ]);

        // Data min and max
        $data = $this->payload;
        $data['name'] = Str::random(256);
        $data['password'] = Str::random(7);
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name', 'password'],
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

        // Unique email
        $users = User::factory(2)
            ->withPassword('12345678')
            ->create();
        $data = $this->payload;
        $data['email'] = $users->random()->{User::EMAIL};
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJson([
                'message' => __('Validation errors'),
                'errors' => [
                    'email' => ['The email has already been taken.']
                ],
            ]);
    }
}
