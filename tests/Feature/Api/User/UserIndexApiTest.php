<?php

namespace Tests\Feature\Api\User;

use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use Illuminate\Database\Eloquent\Collection;
use App\Modules\User\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserIndexApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/users';
    private string $token;
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
        $this->token = $this->getAccessToken($this->userAuth);
    }

    public function test_index_invalid_token_401()
    {
        $this->assertEndpointRequiresAuth(self::GET, $this->api);
    }

    public function test_index_200()
    {
        $total = $this->users->count();

        $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->getJson($this->api)
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data'
            ])
            ->assertJsonPath('message', __('OK'))
            ->assertJsonCount($total, 'data');
    }

    public function test_index_search_200()
    {
        // $names = $this->users->pluck(User::NAME)->toArray();
        // $dataString = $this->getMostRepeatedSubstring($names, 2);
        $search = 'a'; /* $dataString['substring'] ?? ''; */
        $query = http_build_query([
            'search' => $search,
        ]);
        $total = $this->userRepo->queryBySearch($search)->count();

        $this->withHeaders(['Authorization' => "Bearer {$this->token}"])
            ->getJson("{$this->api}?{$query}")
            ->assertOk()
            ->assertJsonPath('message', __('OK'))
            ->assertJsonCount($total, 'data');
    }
}
