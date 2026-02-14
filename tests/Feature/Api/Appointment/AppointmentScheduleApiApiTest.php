<?php

namespace Tests\Feature\Api\Appointment;

use App\Modules\Appointment\Models\Appointment;
use App\Modules\Appointment\Repositories\AppointmentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Api\ApiTestCase;

class AppointmentScheduleApiApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/appointments/schedule';
    private string $token;
    protected AppointmentRepository $appointmentRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->appointmentRepo = new AppointmentRepository(new Appointment);

        $userAuth = $this->superAdmin();
        $this->token = $this->getAccessToken($userAuth);
    }

    public function test_store_unauthorized_401()
    {
        $this->assertEndpointRequiresAuth(self::GET, $this->api);
    }

    public function test_schedule_forbidden_403()
    {
        $this->assertEndpointReturnsForbidden(
            self::GET,
            $this->api
        );
    }

    public function test_schedule_200()
    {
        $response = $this->withHeaders(['Authorization' => "Bearer {$this->token}",])
            ->getJson($this->api)
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => [
                        'date',
                        'appointments' => [
                            '*' => [
                                'id',
                                'patient_id',
                                'dentist_id',
                                'start',
                                'end',
                                'duration',
                                'reason',
                                'treatment_ids' => []
                            ]
                        ]
                    ]
                ]
            ])
            ->assertJsonPath('message', __('OK'));
    }
}
