<?php
namespace App\Services;

use App\Models\Lead;
use App\Models\Doctor;
use App\Models\Remark;
use App\Models\Followup;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class AppointmentService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Doctor::class;
    }

    public function getStoreValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'department' => ['sometimes', 'string']
        ];
    }

    public function getUpdateValidationRules(): array
    {
        return [
            'name' => ['required', 'string'],
            'department' => ['sometimes','nullable', 'string']
        ];
    }

    public function processAndStore($request){

        $lead = Lead::find($request->lead_id);
        $lead->status = "Converted";
        $lead->save();

        $appointment = Appointment::create([
            'lead_id' => $request->lead_id,
            'doctor_id' => $request->doctor,
            'appointment_date' => $request->appointment_date
        ]);

        if ($request->no_followup) {

            Remark::create([
                'remarkable_type' => Lead::class,
                'remarkable_id' => $lead->id,
                'remark' => 'Appointment fixed on ' . $appointment->appointment_date,
                'user_id' => Auth::id(),
            ]);

            return ['success' => true, 'message' => 'Appointment fixed', 'converted' => true, 'lead' => $lead, 'appointment' => $appointment];

        } else {

            $followup = Followup::where('id', $request->followup_id)->with('remarks')->get()->first();
            $followup->converted = true;
            // $followup->actual_date = date('Y-m-d');
            $followup->save();

            if (count($followup->remarks) < 1) {
                Remark::create([
                    'remarkable_type' => Followup::class,
                    'remarkable_id' => $followup->id,
                    'remark' => 'Appointment fixed on ' . $appointment->appointment_date,
                    'user_id' => Auth::id(),
                ]);
            }

            return ['success' => true, 'message' => 'Remark added and converted', 'converted' => true, 'followup' => $followup, 'lead' => $lead, 'appointment' => $appointment];
        }
    }
}
?>
