<?php
namespace App\Services;

use Carbon\Carbon;
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
        $today = Carbon::now();
        $appointment_date = Carbon::parse($request->appointment_date);
        $followup_date = Carbon::parse($request->followup_date);
        if($followup_date->lessThan($appointment_date)){
            return ['success'=>false, 'message'=>'Follow up date should be after Appointment date'];
        }
        if($appointment_date->isPast() && !$appointment_date->isToday()){
            return ['success'=>false, 'message'=>'You cannot input previous dates.'];
        }

        $lead = Lead::find($request->lead_id);
        $lead->status = "Appointment Fixed";
        $lead->followup_created = true;
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

            $followup = Followup::create([
                'lead_id' => $request->lead_id,
                'scheduled_date' => $request->followup_date,
                'converted' => true
            ]);

            return ['success' => true, 'message' => 'Appointment fixed', 'converted' => true, 'lead' => $lead, 'appointment' => $appointment, 'followup'=>$followup];

        } else {

            $followup = Followup::where('id', $request->followup_id)->with('remarks')->get()->first();
            $followup->converted = true;
            $followup->actual_date = Carbon::now();
            $followup->next_followup_date = $request->followup_date;
            $followup->user_id = Auth::user()->id;
            $followup->save();

            Remark::create([
                'remarkable_type' => Followup::class,
                'remarkable_id' => $followup->id,
                'remark' => 'Appointment fixed on ' . $appointment->appointment_date,
                'user_id' => Auth::id(),
            ]);

            $next_followup = Followup::create([
                'lead_id' => $request->lead_id,
                'scheduled_date' => $request->followup_date,
                'converted' => true
            ]);

            return ['success' => true, 'message' => 'Remark added and converted', 'converted' => true, 'followup' => $followup, 'lead' => $lead, 'appointment' => $appointment,'next_followup'=>$next_followup];
        }
    }

    public function processConsult($lead_id, $followup_id, $remark)
    {
        $lead = Lead::where('id',$lead_id)->with('appointment')->get()->first();
        $date = Carbon::createFromFormat('d-m-Y', substr($lead->appointment->appointment_date,0,10));

        if($date->isPast()){
            $followup = Followup::find($followup_id);
            $followup->consulted = true;
            $followup->save();
            $lead->status = 'Consulted';
            $lead->save();
            $appointment = null;
            if($remark){
                $a = Appointment::find($lead->appointment->id);
                $a->remarks = $remark;
                $a->save();
                $appointment = $a;
            }
            return ['success'=>true, 'lead'=>$lead, 'followup'=>$followup, 'appointment'=>$appointment, 'message'=>'Consult is marked'];
        }
        return ['success'=>false, 'lead'=>$lead, 'message'=>'Appointment date has not reached'];
    }

}
