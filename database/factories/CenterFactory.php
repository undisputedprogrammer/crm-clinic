<?php

namespace Database\Factories;

use App\Models\Center;
use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Center>
 */
class CenterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public $model = Center::class;
    public function definition(): array
    {
        $hospital = Hospital::get()->random();
        return [
            'name'=>fake()->company(),
            'hospital_id'=>$hospital->id,
            'location'=>fake()->city(),
            'email'=>fake()->email(),
            'phone'=>fake()->phoneNumber()
        ];
    }
}
