<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Services\JournalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class JournalController extends SmartController
{
    private $jService;

    public function __construct(JournalService $jService)
    {
        $this->jService = $jService;
    }

    public function store(Request $request){

        $response = $this->jService->storeJournal($request);

        if(isset($response['error'])){
            return response()->json(['success'=>false, 'message'=>'An error occured','error'=>$response['error']]);
        }
        elseif(isset($response['existingJournal'])){
            return response()->json(['success'=>true, 'message'=>'Journal Updated!','journal'=>$response['existingJournal']]);
        }
        return response()->json(['success'=>true, 'message'=>'Journal Created!','journal'=>$response['journal']]);
    }

    public function fetch(Request $request){
        if($request->month){
            $date = Carbon::createFromFormat('Y-m',$request->month);
            $month = $date->format('m');
            $year = $date->format('Y');
        }else{
            $today = Carbon::now();
            $month  = $today->format('m');
            $year = $today->format('Y');
        }

        $journalsQuery = Journal::where('user_id',$request->user_id)->whereMonth('date',$month)->whereYear('date',$year);
        $journals = $journalsQuery->latest()->get();
        return response($journals);
    }
}
