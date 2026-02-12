<?php

namespace Database\Seeders;

use App\Modules\User\Models\User;
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
            'email' => 'ecepcionista@pruebasmulhacen.com',
            'password' => Hash::make('0dHGgfh49v'),
        ]);

        // $users = User::factory(20)
        //     ->withPassword('12345678')
        //     ->create();

        $this->command->info(self::class . ' is finished');
    }
}
