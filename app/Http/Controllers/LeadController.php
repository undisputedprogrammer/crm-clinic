<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class LeadController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function change(Request $request){
        $lead = Lead::find($request->lead_id);
        $lead->customer_segment = $request->customer_segment;
        $lead->save();
        return response()->json(['success'=>true, 'message'=>'Customer Segment Updated']);
    }

    public function changevalid(Request $request){
        $lead = Lead::find($request->lead_id);
        if($request->is_valid == true){
            $lead->is_valid = false;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Valid status set to false','is_valid'=>0]);
        }
        elseif($request->is_valid == false){
            $lead->is_valid = true;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Valid status set to true','is_valid'=>1]);
        }


        return response('Something went wrong',400);
    }

    public function changeGenuine(Request $request){
        $lead = Lead::find($request->lead_id);
        if($request->is_genuine == true){
            $lead->is_genuine = false;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Genuine status set to false','is_genuine'=>0]);
        }
        elseif($request->is_genuine == false){
            $lead->is_genuine = true;
            $lead->save();
            return response()->json(['success'=>true,'message'=>'Genuine status set to true','is_genuine'=>1]);
        }

        return response('Something went wrong',400);
    }
}
