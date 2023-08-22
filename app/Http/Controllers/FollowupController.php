<?php

namespace App\Http\Controllers;

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

    public function initiate(Request $request){
        $followup = Followup::create([
            'lead_id'=>$request->lead_id,
            'scheduled_date'=>$request->scheduled_date
        ]);
        $lead = Lead::find($request->lead_id);
        $lead->followup_created = true;
        $lead->save();
        return response()->json(['success'=>true, 'message'=>'Follow up has been initiated for this lead','followup'=>$followup]);
    }

    public function store(Request $request){

        if($request->remark != null && $request->next_followup_date != null){

            $followup_remark = Remark::create([
                'remarkable_type'=>Followup::class,
                'remarkable_id'=>$request->followup_id,
                'remark'=>$request->remark,
                'user_id'=>$request->user()->id
            ]);


                $followup = Followup::find($request->followup_id);
                $followup->actual_date = date('Y-m-d');
                $followup->next_followup_date = $request->next_followup_date;
                $followup->save();
                $next_followup = Followup::create([
                    'lead_id'=>$request->lead_id,
                    'scheduled_date'=>$request->next_followup_date,
                ]);

                return response()->json(['success'=>true, 'message'=>'Follow up processed','followup'=>$followup,'next_followup'=>$next_followup,'remarks'=>$followup->remarks,'followup_remark'=>$followup_remark]);

        }
        elseif($request->remark != null && $request->next_followup_date == null){

            $followup_remark = Remark::create([
                'remarkable_type'=>Followup::class,
                'remarkable_id'=>$request->followup_id,
                'remark'=>$request->remark,
                'user_id'=>$request->user()->id
            ]);

            return response()->json(['success'=>true, 'message'=>'Follow up processed','followup_remark'=>$followup_remark]);
        }

        // $followup_remark = Remark::create([
        //     'remarkable_type'=>Followup::class,
        //     'remarkable_id'=>$request->followup_id,
        //     'remark'=>$request->remark,
        //     'user_id'=>$request->user()->id
        // ]);

        elseif($request->next_followup_date != null) {
            $followup = Followup::find($request->followup_id);
            $followup->actual_date = date('Y-m-d');
            $followup->next_followup_date = $request->next_followup_date;
            $followup->save();
            $next_followup = Followup::create([
                'lead_id'=>$request->lead_id,
                'scheduled_date'=>$request->next_followup_date,
            ]);

            return response()->json(['success'=>true, 'message'=>'Follow up processed','followup'=>$followup,'next_followup'=>$next_followup,'remarks'=>$followup->remarks]);
        }





        // return response()->json(['success'=>true, 'message'=>'Follow up processed','followup_remark'=>$followup_remark]);
    }
}
