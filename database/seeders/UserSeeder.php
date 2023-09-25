<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::factory()->count(5)->create();

        foreach($users as $user){
            $user->assignRole('agent');
        }

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@demo.com',
            'center_id' => null,
            'email_verified_at' => now(),
            'password' => Hash::make('abcd1234'),
            'remember_token' => Str::random(10),
        ]);

        $admin->assignRole('admin');

        $agent = User::create([
            'name' => 'Muhammed Ali',
            'email' => 'ali@demo.com',
            'center_id'=> Center::get()->random()->id,
            'email_verified_at' => now(),
            'password' => Hash::make('abcd1234'),
            'remember_token' => Str::random(10),
        ]);

        $agent->assignRole('agent');

    }
}
