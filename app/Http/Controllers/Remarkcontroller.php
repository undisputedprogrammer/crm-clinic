<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\Lead;
use App\Models\Remark;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class Remarkcontroller extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function store(Request $request){
        // if($request->remark_type == 'lead'){
        //     $is_followup = false;
        // }
        // else{
        //     $is_followup = true;
        // }
        Remark::create([
            'remarkable_type'=>Lead::class,
            'remarkable_id'=>$request->remarkable_id,
            'remark'=>$request->remark,
            'user_id'=>$request->user()->id
        ]);
        return response()->json(['success'=>true,'message'=>'New remark has been added']);
    }

    public function getRemarks(Request $request){
        $remarkable_type = null;
        if($request->remarkable_type == 'lead'){
            $remarkable_type = Lead::class;
        }

        $remarks = Remark::where('remarkable_id',$request->remarkable_id)->where('remarkable_type','App\Models\Lead')->get();

        return response()->json(['remarks'=>$remarks,'remarkable_type'=>$remarkable_type]);
    }

    public function followup(Request $request){
        $followups = Followup::where('lead_id',$request->lead_id)->with('remarks')->orderBy('created_at','desc')->get();
        return response()->json(['followup'=>$followups]);
    }
}