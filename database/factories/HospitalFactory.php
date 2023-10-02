<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Testing\Fakes\Fake;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hospital>
 */
class HospitalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $model = Hospital::class;
    public function definition(): array
    {
        $name = fake()->company();
        return [
            'name'=> $name,
            'ho_location'=>fake()->city(),
            'email'=>fake()->email(),
            'phone'=>fake()->phoneNumber(),
            'main_cols' => json_encode([]),
            'chat_room_id' => 'hos_'.substr($name, 0, 3)
        ];
    }
}
