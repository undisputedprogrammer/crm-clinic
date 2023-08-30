<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Remark;
use App\Models\Followup;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AppointmentController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function store(Request $request){

                $followup = Followup::where('id',$request->followup_id)->with('remarks')->get()->first();
                $followup->converted = true;
                $followup->actual_date = date('Y-m-d');
                $followup->save();
                if(count($followup->remarks)<1){
                    Remark::create([
                        'remarkable_type'=>Remark::class,
                        'remarkable_id'=>$followup->id,
                        'remark'=>'Appointment Fixed',
                        'user_id'=>Auth::id(),
                    ]);
                }
                $lead = Lead::find($request->lead_id);
                $lead->status='Converted';
                $lead->save();
                $appointment = Appointment::create([
                    'lead_id'=>$request->lead_id,
                    'doctor_id'=>$request->doctor,
                    'appointment_date'=>$request->appointment_date
                ]);
                return response()->json(['success'=>true, 'message'=>'Remark added and converted', 'converted'=>true, 'followup'=>$followup, 'lead'=>$lead,'appointment'=>$appointment]);

    }

}
