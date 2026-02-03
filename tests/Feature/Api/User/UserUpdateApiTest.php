<?php

namespace Tests\Feature\Api\User;

use Illuminate\Support\Str;
use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use App\Modules\User\Resources\UserResource;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserUpdateApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/users/:id';
    private string $token;
    private array $payload = [];
    protected UserRepository $userRepo;
    protected Collection $users;
    protected User $userAuth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = new UserRepository(new User);

        $this->users = User::factory(2)->create();
        $this->userAuth = $this->users->first();
        $this->payload = [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'password' => 'password123',
        ];
        $this->token = $this->getAccessToken($this->userAuth);
    }

    public function test_update_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(
            self::PUT,
            str_replace(':id', $this->userAuth->{User::ID}, $this->api),
            $this->payload
        );
    }

    public function test_update_200()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->putJson(
                str_replace(':id', $this->userAuth->{User::ID}, $this->api),
                $this->payload
            )
            ->assertOk();

        $oldPasswordHash = $this->userAuth->{User::PASSWORD};
        $updateData = json_decode((new UserResource($this->userAuth->refresh()))->toJson(), true);

        $response->assertJson([
            'message' => __('OK'),
            'data' => $updateData,
        ])
            ->assertJsonPath('data.email', $this->payload['email'])
            ->assertJsonPath('data.name', $this->payload['name']);

        $this->assertNotEquals($oldPasswordHash, $this->userAuth->{User::PASSWORD});
    }

    public function test_update_404()
    {
        $this->assertEndpointReturnsNotFound(
            self::PUT,
            str_replace(':id', Str::uuid(), $this->api),
            $this->payload,
            $this->token
        );
    }

    public function test_update_validation_422(): void
    {
        // Data user_id invalid UUID and required fields
        $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->putJson(str_replace(':id', 'invalid-uuid', $this->api))
            ->assertStatus(422)
            ->assertJsonPath('message', __('Validation errors'))
            ->assertJsonStructure([
                'message',
                'errors' => ['user', 'email', 'name', 'password'],
            ]);

        // Data min and max
        $data = $this->payload;
        $data['name'] = Str::random(256);
        $data['password'] = Str::random(7);
        $this->putJson(
            str_replace(':id', $this->userAuth->{User::ID}, $this->api),
            $data
        )
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name', 'password'],
            ]);

        // Data invalid email
        $data = $this->payload;
        $data['email'] = Str::random(length: 10);
        $this->putJson(
            str_replace(':id', $this->userAuth->{User::ID}, $this->api),
            $data
        )
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);

        // Data unique email
        $data = $this->payload;
        $data['email'] = $this->users->last()->{User::EMAIL};
        $this->putJson(
            str_replace(':id', $this->userAuth->{User::ID}, $this->api),
            $data
        )
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }
}
