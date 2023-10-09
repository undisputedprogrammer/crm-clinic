<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Followup;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lead>
 */
class FollowupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Followup::class;

    public function definition(): array
    {
        $check = random_int(1, 10);
        $followup = $check > 2;
        $lead = Lead::all()->random();
        $dint = random_int(0,2);
        $sd = (Carbon::now()->startOfMonth()->addDays($dint))->format('Y-m-d');
        $ad = null;
        $converted = false;
        if ($followup) {
            $ad = $sd;
            $converted = $check > 8;
        }
        return [
            'lead_id' => $lead->id,
            'followup_count' => 1,
            'scheduled_date' => $sd,
            'actual_date' => $ad,
            'next_followup_date' => null,
            'converted' => $converted,
            'consulted' => false,
            'user_id' => $lead->assigned_to,
        ];
    }
    public function configure(): static
    {
        return $this->afterCreating(function (Followup $f) {
            $lead = $f->lead;
            $check = random_int(1, 10);
            $x = explode(' ', $f->scheduled_date)[0];
            $x = explode('-', $x);
            $y = implode('-', $x);
            if ($f->converted) {
                $dt = ((Carbon::createFromFormat('Y-m-d', $y))->addDays(2))->format('Y-m-d');
                $a = Appointment::factory()->create([
                    'lead_id' => $f->lead_id,
                    'appointment_date' => $dt,
                    'consulted_date' => $dt
                ]);
                $lead->status = 'Appointment Fixed';
                $consulted = $check > 5;
                if ($consulted) {
                    $a->consulted_date = Carbon::createFromFormat('d-m-Y', $a->appointment_date)->format('Y-m-d');
                    $a->save();
                    $dx = Carbon::createFromFormat('Y-m-d', $a->consulted_date)->addDay();
                    Followup::factory()->create(
                        [
                            'lead_id' => $lead->id,
                            'followup_count' => 2,
                            'scheduled_date' => $dx,
                            'actual_date' => $dx,
                            'next_followup_date' => null,
                            'converted' => true,
                            'consulted' => true,
                            'user_id' => $lead->assigned_to,
                        ]
                    );
                    $f->consulted = true;
                    $lead->status = 'Consulted';
                }
                $lead->save();
            } else {
                $check = random_int(1, 10);
                if ($check > 2) {
                    $lead->status = 'Follow-up Started';
                } else {
                    $lead->status = 'Closed';
                }
                $lead->save();
            }
        });
    }
}
