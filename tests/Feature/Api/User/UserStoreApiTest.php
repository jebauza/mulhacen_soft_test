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
    protected User $userAuth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = new UserRepository(new User);

        $this->userAuth = User::factory()->create();
        $this->payload = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'password123',
        ];
        $this->token = $this->getAccessToken($this->userAuth);
    }

    public function test_store_201()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->postJson($this->api, $this->payload)
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id'
                ],
            ])
            ->assertJsonPath('message', __('Created'));

        $user = $this->userRepo->findOrFail($response->json('data.id'));
        $storeData = json_decode((new UserResource($user))->toJson(), true);

        $response->assertJsonPath('data', $storeData);
    }

    public function test_store_invalid_token_401()
    {
        $this->assertEndpointRequiresAuth('get', $this->api);
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
                'errors' => ['email', 'name', 'password'],
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
        $data['email'] = $this->userAuth->{User::EMAIL};
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }
}
