<?php

namespace Tests\Feature\Api\Auth;

use Tests\Feature\Api\ApiTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterApiTest extends ApiTestCase
{
    use RefreshDatabase;

    private $api = 'api/auth/register';

    public function test_register_201(): void
    {
        $this->postJson($this->api, [
            'name' => 'Test',
            'surname' => 'Test',
            'email' => 'test@gmail.com',
            'password' => 'test123456789',
        ])
        ->assertStatus(201)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
            'expires_at',
            'user',
        ]);
    }

    public function test_register_validation_with_invalid_data_422(): void
    {
        $this->postJson($this->api, [
            'email' => 'test',
        ])
            ->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => ['name', 'password'],
            ]);
    }
}
