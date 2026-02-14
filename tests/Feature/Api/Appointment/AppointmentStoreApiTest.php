<?php

namespace Tests\Feature\Api\Appointment;

use App\Modules\Appointment\Models\Appointment;
use App\Modules\Appointment\Repositories\AppointmentRepository;
use App\Modules\Appointment\Resources\AppointmentResource;
use App\Modules\Patient\Models\Patient;
use App\Modules\Treatment\Models\Treatment;
use App\Modules\User\Models\Dentist;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\Feature\Api\ApiTestCase;

class AppointmentStoreApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/appointments';
    private string $token;
    private array $payload = [];
    protected AppointmentRepository $appointmentRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->appointmentRepo = new AppointmentRepository(new Appointment);

        $userAuth = $this->superAdmin();
        $this->token = $this->getAccessToken($userAuth);
        $this->payload = [
            "patient_id" => Patient::first()->{Patient::ID},
            "dentist_id" => Dentist::first()->{Dentist::ID},
            "start" => "2026-01-12 08:30:00",
            "end" => "2026-01-12 08:45:00",
            "duration" => 15,
            "reason" => "porque quiero",
            "treatment_ids" => Treatment::inRandomOrder()
                ->limit(2)
                ->pluck(Treatment::ID)
                ->toArray(),
        ];
    }

    public function test_store_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(self::POST, $this->api);
    }

    public function test_store_forbidden_403()
    {
        $this->assertEndpointReturnsForbidden(
            self::POST,
            $this->api,
            $this->payload
        );
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
                    'patient_id',
                    'dentist_id',
                    'start',
                    'end',
                    'duration',
                    'reason',
                    'treatment_ids' => [],
                ]
            ])
            ->assertJsonPath('message', __('Created'))
            ->assertJsonPath('data.patient_id', $this->payload['patient_id'])
            ->assertJsonPath('data.dentist_id', $this->payload['dentist_id'])
            ->assertJsonPath('data.start', $this->payload['start'])
            ->assertJsonPath('data.end', $this->payload['end'])
            ->assertJsonPath('data.duration', $this->payload['duration'])
            ->assertJsonPath('data.reason', $this->payload['reason']);

        $this->assertDatabaseHas(Appointment::TABLE, [
            Appointment::ID => $response->json('data.id'),
            Appointment::PATIENT_ID => $this->payload['patient_id'],
            Appointment::DENTIST_ID => $this->payload['dentist_id'],
            Appointment::START => $this->payload['start'],
            Appointment::END => $this->payload['end'],
            Appointment::DURATION => $this->payload['duration'],
            Appointment::REASON => $this->payload['reason'],
        ]);


        $appointment = $this->appointmentRepo->findWithRelations($response->json('data.id'), ['treatments']);
        $this->assertEqualsCanonicalizing(
            $this->payload['treatment_ids'],
            $appointment->treatments->pluck(Treatment::ID)->toArray()
        );

        $data = json_decode((new AppointmentResource($appointment))->toJson(), true);
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
                'errors' => ['patient_id', 'dentist_id', 'start', 'end', 'duration', 'treatment_ids'],
            ]);

        // Data min
        $data = $this->payload;
        $data['duration'] = 0;
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['duration'],
            ]);

        // Data max
        $data = $this->payload;
        $data['duration'] = 65536;
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['duration'],
            ]);

        // Data not array
        $data = $this->payload;
        $data['treatment_ids'] = 'not an array';
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['treatment_ids'],
            ]);

        // Data treatment_ids.* not uuid
        $data = $this->payload;
        $data['treatment_ids'] = ['not-a-uuid'];
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['treatment_ids.0'],
            ]);

        // UUID not in DB
        $data = $this->payload;
        $data['patient_id'] = Str::uuid()->toString();
        $data['dentist_id'] = Str::uuid()->toString();
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['patient_id', 'dentist_id'],
            ]);

        // UUID not in DB
        $data = $this->payload;
        $data['treatment_ids'] = [Str::uuid()->toString()];
        $this->postJson($this->api, $data)
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['treatment_ids.0'],
            ]);
    }
}
