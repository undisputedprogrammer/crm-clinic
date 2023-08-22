<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class SearchController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(Request $request){

        $followups = Followup::where($request->search_type,'>=',$request->from_date)->where($request->search_type,'<=',$request->to_date)->with(['lead','remarks'])->paginate(10);
        $table = view('partials.search-results-table',compact('followups'))->render();

        return response()->json(['success'=>true, 'message'=>'Path clear! ready the logic','followups'=>$followups,'table_html'=>$table]);
    }
}
