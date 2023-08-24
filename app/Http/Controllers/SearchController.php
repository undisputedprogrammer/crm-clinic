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


        $query = Followup::where($request->search_type, '>=', $request->from_date)
                ->where($request->search_type, '<=', $request->to_date)
                ->where('actual_date', null)
                ->with(['lead', 'remarks']);

        if ($request->is_valid !== null && $request->is_valid != 'null') {
            $query->whereHas('lead', function($query) use($request){
                $query->where('is_valid',$request->is_valid);
            });
        }

        if ($request->is_genuine !== null && $request->is_genuine != 'null') {
            $query->whereHas('lead', function($query) use($request){
                $query->where('is_genuine',$request->is_genuine);
            });
        }

        if ($request->lead_status !== null && $request->lead_status != 'null') {
            $query->whereHas('lead', function($query) use($request){
                $query->where('status',$request->lead_status);
            });
        }

        $followups = $query->paginate(10);

        $table = view('partials.search-results-table',compact('followups'))->render();

        return response()->json(['success'=>true, 'message'=>'Search successful','followups'=>$followups,'table_html'=>$table,'pagination_type'=>$request->search_type,'request'=>$request->is_valid]);
    }
}
