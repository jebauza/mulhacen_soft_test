<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserFakeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('123456789'),
        ]);

        $users = User::factory(20)
            ->withPassword('12345678')
            ->create();

        $this->command->info(self::class . ' is finished');
    }
}
