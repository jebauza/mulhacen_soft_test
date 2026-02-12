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
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'email',
                ]
            ])
            ->assertJsonPath('message', __('OK'))
            ->assertJsonPath('data.id', $this->userAuth->{User::ID})
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
                'errors' => ['email', 'password', 'user'],
            ]);

        $api = str_replace(':id', $this->userAuth->{User::ID}, $this->api);

        // Data min and max
        $data = $this->payload;
        $data['password'] = Str::random(7);
        $this->putJson($api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['password'],
            ]);

        // Data invalid email
        $data = $this->payload;
        $data['email'] = Str::random(length: 10);
        $this->putJson($api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);

        // Data unique email
        $data = $this->payload;
        $data['email'] = $this->users->last()->{User::EMAIL};
        $this->putJson($api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['email'],
            ]);
    }
}
