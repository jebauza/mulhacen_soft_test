<?php

namespace Tests\Feature\Api\User;

use Illuminate\Support\Str;
use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserStoreApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/users';
    private string $token;
    private array $payload = [];
    protected UserRepository $userRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = new UserRepository(new User);

        $this->payload = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];
        $this->token = $this->getAccessToken(User::factory()->create());
    }

    public function test_store_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(self::POST, $this->api, $this->payload);
    }

    public function test_store_201()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->postJson($this->api, $this->payload)
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'email',
                ]
            ])
            ->assertJsonPath('message', __('Created'))
            ->assertJsonPath('data.email', $this->payload['email']);

        $this->assertDatabaseHas(User::TABLE, [
            User::ID => $response->json('data.id'),
            User::EMAIL => $this->payload['email'],
        ]);

        $this->assertDatabaseMissing(User::TABLE, [
            User::EMAIL => $this->payload['email'],
            User::PASSWORD => $this->payload['password'], // Password should be hashed, so the raw value must not exist in DB
        ]);

        $user = $this->userRepo->find($response->json('data.id'));
        $data = json_decode((new UserResource($user))->toJson(), true);

        $response->assertJsonPath('data', $data);
    }

    public function test_store_validation_422(): void
    {
        // Data required
        $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->postJson($this->api)
            ->assertStatus(422)
            ->assertJsonPath('message', __('Validation errors'))
            ->assertJsonStructure([
                'message',
                'errors' => ['email', 'password'],
            ]);

        // Data min and max
        $data = $this->payload;
        $data['password'] = Str::random(7);
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password'],
            ]);

        // Data invalid email
        $data = $this->payload;
        $data['email'] = Str::random(length: 10);
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);

        // Data unique email
        $data = $this->payload;
        $data['email'] = $this->userRepo->random()->{User::EMAIL};
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }
}
