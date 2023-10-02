<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Center;
use App\Models\Hospital;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            ],
            'chat_room_id' => 'hos_craft',
            'centers' => [
                'Caft_Kodungallur',
                'Malappuram',
                'Vyttila'
            ]
        ],
        [
            'name' => 'AR',
            'ho_location' => 'Kodungalloor',
            'email' => 'info@ar.com',
            'phone' => '1234512345',
            'main_cols' => [
                'name' => 'name',
                'email' => 'email',
                'phone' => 'contact_number',
                'city' => 'city'
            ],
            'chat_room_id' => 'hos_ar',
            'centers' => [
                'AR_Kodungallur'
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
            $centers = $h['centers'];
            unset($h['centers']);
            $hosp = Hospital::factory()->create($h);
            foreach ($centers as $c) {
                $c = Center::factory()->create([
                    'hospital_id' => $hosp->id
                ]);
                $c->users()->save(User::factory()->create([
                    'hospital_id' => $hosp->id
                ]));
            }
        }
    }
}
