<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Remark;
use App\Models\Followup;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Services\AppointmentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AppointmentController extends SmartController
{


    public function store(Request $request){


                if($request->no_followup){
                    $lead = Lead::find($request->lead_id);
                    $lead->status="Converted";
                    $lead->save();

                    $appointment = Appointment::create([
                        'lead_id'=>$request->lead_id,
                        'doctor_id'=>$request->doctor,
                        'appointment_date'=>$request->appointment_date
                    ]);

                    Remark::create([
                        'remarkable_type'=>Lead::class,
                        'remarkable_id'=>$lead->id,
                        'remark'=>'Appointment fixed on '.$appointment->appointment_date,
                        'user_id'=>Auth::id(),
                    ]);

                    return response()->json(['success'=>true, 'message'=>'Appointment fixed', 'converted'=>true, 'lead'=>$lead,'appointment'=>$appointment]);
                }
                else{

                    $lead = Lead::find($request->lead_id);
                    $lead->status="Converted";
                    $lead->save();

                    $followup = Followup::where('id',$request->followup_id)->with('remarks')->get()->first();
                    $followup->converted = true;
                    $followup->actual_date = date('Y-m-d');
                    $followup->save();


                    $appointment = Appointment::create([
                        'lead_id'=>$request->lead_id,
                        'doctor_id'=>$request->doctor,
                        'appointment_date'=>$request->appointment_date
                    ]);

                    if(count($followup->remarks)<1){
                        Remark::create([
                            'remarkable_type'=>Followup::class,
                            'remarkable_id'=>$followup->id,
                            'remark'=>'Appointment fixed on '.$appointment->appointment_date,
                            'user_id'=>Auth::id(),
                        ]);
                    }

                return response()->json(['success'=>true, 'message'=>'Remark added and converted', 'converted'=>true, 'followup'=>$followup, 'lead'=>$lead,'appointment'=>$appointment]);

                }

    }

    public function __construct(Request $request, AppointmentService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function index()
    {
        $query = Appointment::orderBy('appointment_date', 'asc');
        if (isset($this->request->from)) {
            $query->where('appointment_date', '>=', $this->request->from);
        }
        if (isset($this->request->to)) {
            $query->where('appointment_date', '<=', $this->request->to);
        }
        $appointments = $query->paginate(10);
        return $this->buildResponse('pages.appointments',['appointments' => $appointments]);
    }
}
