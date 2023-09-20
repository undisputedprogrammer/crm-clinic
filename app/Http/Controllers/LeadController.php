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

    public function answer(Request $request){
        $lead = Lead::find($request->lead_id);

        if($lead == null){
            return response()->json(['success'=>false,'message'=>'Lead not found']);
        }

        if($request->question == 'q_visit'){

            if($request->q_answer == 'null'){
                $lead->q_visit = null;
            }
            $lead->q_visit = $request->q_answer;

            if($lead->q_visit == 'yes'){
                $lead->customer_segment = 'hot';
            }elseif($lead->q_visit == 'no'){
                $lead->customer_segment = 'cold';
            }elseif($lead->q_visit == null || $lead->q_visit == 'null'){
                $lead->customer_segment = null;
            }
            $lead->save();

            return response()->json(['success'=>true, 'message'=>'Response Marked','q_visit'=>$lead->q_visit,'customer_segment'=>$lead->customer_segment,'answer'=>$request->q_answer]);
        }

        if($request->question == 'q_decide'){
            if($request->q_answer == 'null'){
                $lead->q_decide = null;
            }
            else{
                $lead->q_decide = $request->q_answer;
            }

            if($lead->q_decide == 'yes'){
                $lead->customer_segment = 'warm';
            }
            if($lead->q_decide == null || $lead->q_decide == 'null'){
                $lead->customer_segment = 'cold';
            }
            if($lead->q_decide == 'no'){
                $lead->customer_segment = 'cold';
            }

            $lead->save();

            return response()->json(['success'=>true, 'message'=>'Response Marked','q_decide'=>$lead->q_decide,'customer_segment'=>$lead->customer_segment,'answer'=>$request->q_answer]);


        }
    }

    public function close(Request $request){
        $lead = Lead::find($request->lead_id);

        if($lead ==  null){
            return response()->json(['success'=>false,'message'=>'Lead not found']);
        }

        $lead->status = 'Closed';

        $lead->save();

        return response()->json(['success'=>true, 'message'=>'Lead closed successfully']);
    }
}
