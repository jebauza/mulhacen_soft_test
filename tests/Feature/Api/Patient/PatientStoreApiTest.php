<?php

namespace Tests\Feature\Api\Patient;

use Illuminate\Support\Str;
use App\Modules\User\Models\User;
use Tests\Feature\Api\ApiTestCase;
use App\Modules\Patient\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Modules\Patient\Repositories\PatientRepository;
use App\Modules\Patient\Resources\PatientResource;

class PatientStoreApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/patients';
    private string $token;
    private array $payload = [];
    protected PatientRepository $patientRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->patientRepo = new PatientRepository(new Patient);

        $userAuth = User::factory()->create();
        $this->token = $this->getAccessToken($userAuth);
        $this->payload = [
            "name" => "John Doe",
            "email" => "john.doe@example.com",
            "phone" => "+15551234567",
            "notes" => "Test patient created by automated test.",
        ];
    }

    public function test_store_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(self::POST, $this->api);
    }

    public function test_store_201()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->postJson($this->api, $this->payload)
            ->assertCreated()
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'name',
                    'email',
                    'phone',
                    'notes',
                ]
            ])
            ->assertJsonPath('message', __('Created'))
            ->assertJsonPath('data.name', $this->payload['name'])
            ->assertJsonPath('data.email', $this->payload['email'])
            ->assertJsonPath('data.phone', $this->payload['phone'])
            ->assertJsonPath('data.notes', $this->payload['notes']);

        $this->assertDatabaseHas(Patient::TABLE, [
            Patient::ID => $response->json('data.id'),
            Patient::NAME => $this->payload['name'],
            Patient::EMAIL => $this->payload['email'],
            Patient::PHONE => $this->payload['phone'],
            Patient::NOTES => $this->payload['notes'],
        ]);

        $patient = $this->patientRepo->find($response->json('data.id'));
        $data = json_decode((new PatientResource($patient))->toJson(), true);
        $response->assertJsonPath('data', $data);
    }

    public function test_store_validation_422()
    {
        // Data required
        $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->postJson($this->api)
            ->assertStatus(422)
            ->assertJsonPath('message', __('Validation errors'))
            ->assertJsonStructure([
                'message',
                'errors' => ['name', 'email', 'phone'],
            ]);

        // Data max
        $data = $this->payload;
        $data['name'] = Str::random(256);
        $data['email'] = Str::random(256);
        $data['phone'] = Str::random(256);
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name', 'email', 'phone'],
            ]);

        // // Name already exists DB
        // $data = $this->payload;
        // $data['email'] = $this->patientRepo->random()->{Patient::EMAIL};
        // $data['phone'] = $this->patientRepo->random()->{Patient::PHONE};
        // $this->postJson($this->api, $data)
        //     ->assertStatus(422)
        //     ->assertJsonStructure([
        //         'message',
        //         'errors' => ['email', 'phone'],
        //     ]);
    }
}
