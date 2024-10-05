<?php

namespace Database\Seeders;

use App\Models\User;
use Bouncer;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->createMany([
            [
                'first_name' => 'Luke',
                'last_name' => 'Skywalker',
                'email' => 'luke@jedi.com',
                'email_verified_at' => now(),
                'password' => bcrypt('123123'),
            ],
            [
                'first_name' => 'Darth',
                'last_name' => 'Vader',
                'email' => 'vader@jedi.com',
                'email_verified_at' => now(),
                'password' => bcrypt('123123'),
            ]
        ]);

        Bouncer::assign('admin')->to($users->all());
    }
}
