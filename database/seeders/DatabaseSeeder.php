<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\LeadSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AnswerSeeder;
use Database\Seeders\RemarkSeeder;
use Database\Seeders\MessageSeeder;
use Database\Seeders\QuestionSeeder;
use Ynotz\AccessControl\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::create(['name'=>'admin']);
        Role::create(['name'=>'agent']);

        $this->call([
            UserSeeder::class,
            DoctorSeeder::class,
            QuestionSeeder::class,
            LeadSeeder::class,
            RemarkSeeder::class,
            AnswerSeeder::class,
            MessageSeeder::class,
        ]);


        // \App\Models\User::factory(5)->create()->assignRole('agent');





    }
}
