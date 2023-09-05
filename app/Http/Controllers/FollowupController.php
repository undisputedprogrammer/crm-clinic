<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Lead;
use App\Models\Followup;
use App\Models\Remark;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class FollowupController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function initiate(Request $request)
    {
        $lead = Lead::find($request->lead_id);
        $converted = null;

        $followup = Followup::create([
            'lead_id' => $request->lead_id,
            'scheduled_date' => $request->scheduled_date,

        ]);

        if($lead->status == "Converted"){

            $followup->converted = true;
            $followup->save();
        }

        $lead->followup_created = true;
        $lead->save();
        return response()->json(['success' => true, 'message' => 'Follow up has been initiated for this lead', 'followup' => $followup]);
        // return response()->json(['success'=>true,'message'=>'converted '.$followup->converted]);
    }

    public function store(Request $request)
    {

        $followup_remark = Remark::create([
            'remarkable_type' => Followup::class,
            'remarkable_id' => $request->followup_id,
            'remark' => $request->remark,
            'user_id' => $request->user()->id
        ]);

        return response()->json(['success' => true, 'message' => 'Remark added', 'followup_remark' => $followup_remark]);
    }

    public function next(Request $request)
    {

        $followup = Followup::find($request->followup_id);
        $followup->actual_date = date('Y-m-d');
        $followup->next_followup_date = $request->next_followup_date;
        $followup->save();
        $converted = null;



        $next_followup = Followup::create([
            'lead_id' => $request->lead_id,
            'scheduled_date' => $request->next_followup_date,

        ]);

        if($request->converted == true){
            $next_followup->converted = true;
            $next_followup->save();
        }

        return response()->json(['success' => true, 'message' => 'Next follow up scheduled', 'followup' => $followup, 'next_followup' => $next_followup, 'remarks' => $followup->remarks]);
    }

    public function convert(Request $request)
    {

        $followup = Followup::find($request->followup_id);
        $followup->converted = true;
        $followup->save();
        $lead = Lead::find($request->lead_id);
        $lead->status = 'Converted';
        $lead->save();
        return response()->json(['success' => true, 'message' => 'Lead converted to customer', 'followup' => $followup, 'lead' => $lead]);
    }
}
