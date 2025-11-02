<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Brayni Chavez Caruajulca',
            'email' => 'chavezcaruajulca.12@gmail.com',
            'password' => bcrypt('12345')
        ])->assignRole('Admin');
    }
}
