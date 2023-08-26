<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\LeadSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\AnswerSeeder;
use Database\Seeders\RemarkSeeder;
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
            LeadSeeder::class,
            RemarkSeeder::class,
            QuestionSeeder::class,
            AnswerSeeder::class,
        ]);


        // \App\Models\User::factory(5)->create()->assignRole('agent');





    }
}
