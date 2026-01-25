<?php

namespace Tests\Feature\Api;

use App\Modules\User\Models\User;
use Database\Seeders\UserFakeSeeder;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class ApiTestCase extends BaseTestCase
{
    protected User $superAdmin;
    protected string $AccessToken;

    protected function setUp(): void
    {
        parent::setUp();

        // $this->seed(UserFakeSeeder::class);
    }

    protected function superAdminEmail(): string
    {
        return 'superadmin@example.com';
    }

    protected function superAdminPassword(): string
    {
        return '123456789';
    }

    protected function superAdmin(): User
    {
        $this->superAdmin ??= User::firstWhere(User::EMAIL, $this->superAdminEmail());
        return $this->superAdmin;
    }

    protected function getAccessToken(User $user): string
    {
        $this->AccessToken ??= JWTAuth::fromUser($user);
        return $this->AccessToken;
    }

    protected function assertEndpointRequiresAuth(string $method, string $api, array $data = []): void
    {
        $this->json(strtoupper($method), $api, $data, [
            'Authorization' => 'Bearer invalid_token',
        ])
            ->assertStatus(401)
            ->assertJson([
                'message' => __('Unauthorized'),
                'errors' => [
                    'auth' => [__('Authentication token is invalid or expired')],
                ],
            ]);
    }
}
