<?php

namespace Tests\Feature\Api\User;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserPaginateApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/users/paginate';
    private array $payload = [];
    protected UserRepository $userRepo;
    private Collection $users;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepo = new UserRepository(new User);

        $this->users = User::factory(10)
            ->withPassword('12345678')
            ->create();

        $this->payload = [
            'page' => 2,
            'per_page' => 5,
        ];
    }

    public function test_paginate_200()
    {
        $token = $this->getAccessToken($this->users->random());
        $total = $this->users->count();

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
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
        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson("{$this->api}?{$query}")
            ->assertOk()
            ->assertJsonCount($this->payload['per_page'], 'data')
            ->assertJsonPath('meta', [
                'per_page' => $this->payload['per_page'],
                'current_page' => $this->payload['page'],
                'last_page' => (int) ($total / $this->payload['per_page']),
                'total' => $total,
            ]);
    }

    public function test_paginate_search_200()
    {
        $token = $this->getAccessToken($this->users->random());

        // $names = $this->users->pluck(User::NAME)->toArray();
        // $dataString = $this->getMostRepeatedSubstring($names, 2);
        $search = 'a'; /* $dataString['substring'] ?? ''; */
        $query = http_build_query([
            'search' => $search,
        ]);
        $total = $this->userRepo->queryBySearch($search)->count();

        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson("{$this->api}?{$query}")
            ->assertOk()
            ->assertJsonPath('meta.total', $total);
    }

    public function test_paginate_invalid_token_401()
    {
        $this->assertEndpointRequiresAuth('get', $this->api);
    }

    public function test_paginate_validation_422(): void
    {
        $token = $this->getAccessToken($this->users->random());

        // Data integer
        $query = http_build_query([
            'page'  => 'not integer',
            'per_page' => 'not integer',
        ]);
        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson("{$this->api}?{$query}")
            ->assertStatus(422)
            ->assertJsonPath('message', __('Validation errors'))
            ->assertJsonStructure([
                'message',
                'errors' => ['page', 'per_page'],
            ]);

        // Data min
        $query = http_build_query([
            'page'  => 0,
            'per_page' => 0,
        ]);
        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson("{$this->api}?{$query}")
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['page', 'per_page'],
            ]);

        // Data max
        $query = http_build_query([
            'per_page' => 101,
        ]);
        $this->withHeaders(['Authorization' => "Bearer {$token}",])
            ->getJson("{$this->api}?{$query}")
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['per_page'],
            ]);
    }
}
