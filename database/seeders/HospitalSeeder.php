<?php

namespace Database\Seeders;

use App\Models\Hospital;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HospitalSeeder extends Seeder
{
    private $hospitals = [
        [
            'name' => 'Craft',
            'ho_location' => 'Kodungalloor',
            'email' => 'info@craft.com',
            'phone' => '1234512345',
            'main_cols' => [
                'name' => 'full_name',
                'email' => 'email',
                'phone' => 'phone_number',
                'city' => 'city'
            ]
        ],
        [
            'name' => 'AR',
            'ho_location' => 'Kodungalloor',
            'email' => 'info@ar.com',
            'phone' => '1234512345',
            'main_cols' => [
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Contct Number',
                'city' => 'City'
            ]
        ],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->hospitals as $h) {
            $h['main_cols'] = $h['main_cols'];
            Hospital::factory()->create($h);
        }
    }
}
