<?php

namespace Database\Factories;

use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class LeadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Lead::class;

    public function definition(): array
    {
         $customer_segment = ['hot','warm','cold'];
         $status = ['Created'];

        return [
            'name'=>fake()->name(),
            'phone'=>fake()->phoneNumber(),
            'email'=>fake()->email(),
            'city'=>fake()->city(),
            'is_valid'=>false,
            'is_genuine'=>false,
            'history'=>fake()->paragraph(),
            'customer_segment'=> $customer_segment[array_rand($customer_segment)],
            'status'=> $status[array_rand($status)],
            'followup_created'=>false,
            'assigned_to'=>$this->getAgent(),
        ];
    }

    public function getAgent(){
        $user = User::all()->random();
        if($user->hasRole('agent')){
            return $user->id;
        }
        else{
            return $this->getAgent();
        }
    }
}
