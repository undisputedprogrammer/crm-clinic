<?php

namespace Database\Seeders;

use App\Models\Center;
use App\Models\Hospital;
use App\Models\User;
use App\Models\UserCenter;
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

            UserCenter::create([
                'user_id'=>$user->id,
                'center_id'=>Center::where('hospital_id',$user->hospital_id)->get()->random()->id
            ]);
        }

        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@demo.com',
            'hospital_id'=> 1,
            'email_verified_at' => now(),
            'password' => Hash::make('abcd1234'),
            'remember_token' => Str::random(10),
        ]);

        $admin->assignRole('admin');

        $secondadmin = User::create([
            'name' => 'admin',
            'email' => 'secondadmin@demo.com',
            'hospital_id'=> 2,
            'email_verified_at' => now(),
            'password' => Hash::make('abcd1234'),
            'remember_token' => Str::random(10),
        ]);

        $secondadmin->assignRole('admin');

        $agent = User::create([
            'name' => 'Muhammed Ali',
            'email' => 'ali@demo.com',
            'hospital_id' => 1,
            'email_verified_at' => now(),
            'password' => Hash::make('abcd1234'),
            'remember_token' => Str::random(10),
        ]);

        $agent->assignRole('agent');
        UserCenter::create([
            'user_id'=>$agent->id,
            'center_id'=>Center::get()->random()->id
        ]);

    }
}
