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
    protected $connectorService;

    public function __construct(Request $request, AppointmentService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function store(Request $request)
    {
        $response = $this->connectorService->processAndStore($request);

        return response()->json($response);

    }


    public function index()
    {
        $query = Appointment::with(['lead' => function ($query) {
            return $query->with('remarks');
        }, 'doctor'])->orderBy('appointment_date', 'asc');

        if (isset($this->request->from)) {
            $query->where('appointment_date', '>=', $this->request->from);
        }

        if (isset($this->request->to)) {
            $query->where('appointment_date', '<=', $this->request->to);
        }

        $appointments = $query->paginate(10);

        return $this->buildResponse('pages.appointments', ['appointments' => $appointments]);
    }

    public function consulted(Request $request)
    {
        $lead = Lead::where('id',$request->lead_id)->with('appointment')->get()->first();
        $date = Carbon::createFromFormat('d-m-Y', substr($lead->appointment->appointment_date,0,10));

        if($date->isPast()){
            $followup = Followup::find($request->followup_id);
            $followup->consulted = true;
            $followup->save();
            $lead->status = 'Consulted';
            $lead->save();
            $appointment = null;
            if($request->remark){
                $a = Appointment::find($lead->appointment->id);
                $a->remarks = $request->remark;
                $a->save();
                $appointment = $a;
            }
            return response()->json(['success'=>true, 'lead'=>$lead, 'followup'=>$followup, 'appointment'=>$appointment, 'message'=>'Consult is marked']);
        }
        return response()->json(['success'=>false, 'lead'=>$lead, 'message'=>'Appointment is not over']);
    }
}
