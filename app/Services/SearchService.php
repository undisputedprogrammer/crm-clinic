<?php
namespace App\Services;

use App\Models\Followup;

class SearchService{

    public function getResults($request){

        $query = Followup::where('actual_date', null)
            ->where($request->search_type, '>=', $request->from_date)
            ->where($request->search_type, '<=', $request->to_date)
            ->with(['lead'=>function($q){
                return $q->with('appointment');
            }, 'remarks']);

        $filters = [
            'is_valid' => 'is_valid',
            'is_genuine' => 'is_genuine',
            'lead_status' => 'status',
        ];

        foreach ($filters as $param => $column) {
            if ($request->$param !== null && $request->$param != 'null') {
                $query->whereHas('lead', function ($query) use ($request, $param, $column) {
                    $query->where($column, $request->$param);
                });
            }
        }
        $query->where('consulted',null);

        $followups = $query->paginate(10);

        $table = view('partials.search-results-table', compact('followups'))->render();

        return [
            'success' => true,
            'message' => 'Search successful',
            'followups' => $followups,
            'table_html' => $table,
            'pagination_type' => $request->search_type,
            'request' => $request->is_valid,
        ];
    }
}
