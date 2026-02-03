<?php

namespace Tests\Feature\Api\User;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserOffsetPaginateApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/users/offset-paginate';
    private string $token;
    private array $payload = [];
    protected UserRepository $userRepo;
    private Collection $users;
    protected User $userAuth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = new UserRepository(new User);

        $this->users = User::factory(10)
            ->withPassword('12345678')
            ->create();
        $this->userAuth = $this->users->random();

        $this->payload = [
            'limit' => 5,
            'offset' => 5,
        ];
        $this->token = $this->getAccessToken($this->userAuth);
    }

    public function test_offset_paginate_invalid_token_401()
    {
        $this->assertEndpointRequiresAuth(self::GET, $this->api);
    }

    public function test_offset_paginate_200()
    {
        $total = $this->users->count();

        $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->getJson($this->api)
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data',
                'meta',
            ])
            ->assertJsonPath('message', __('OK'))
            ->assertJsonPath('meta.total', $total);

        // Data with payload
        $query = http_build_query($this->payload);
        $this->getJson("{$this->api}?{$query}")
            ->assertOk()
            ->assertJsonCount($this->payload['limit'], 'data')
            ->assertJsonPath('meta', [
                'limit' => $this->payload['limit'],
                'offset' => $this->payload['offset'],
                'total' => $total,
            ]);
    }

    public function test_offset_paginate_search_200()
    {
        // $names = $this->users->pluck(User::NAME)->toArray();
        // $dataString = $this->getMostRepeatedSubstring($names, 2);
        $search = 'a'; /* $dataString['substring'] ?? ''; */
        $query = http_build_query([
            'search' => $search,
        ]);
        $total = $this->userRepo->queryBySearch($search)->count();

        $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->getJson("{$this->api}?{$query}")
            ->assertOk()
            ->assertJsonPath('meta.total', $total);
    }

    public function test_offset_paginate_validation_422(): void
    {
        // Data integer
        $query = http_build_query([
            'offset'  => 'not integer',
            'limit' => 'not integer',
        ]);
        $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->getJson("{$this->api}?{$query}")
            ->assertStatus(422)
            ->assertJsonPath('message', __('Validation errors'))
            ->assertJsonStructure([
                'message',
                'errors' => ['offset', 'limit'],
            ]);

        // Data min
        $query = http_build_query([
            'offset'  => -1,
            'limit' => 0,
        ]);
        $this->getJson("{$this->api}?{$query}")
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['offset', 'limit'],
            ]);

        // Data max
        $query = http_build_query([
            'limit' => 101,
        ]);
        $this->getJson("{$this->api}?{$query}")
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['limit'],
            ]);
    }
}
