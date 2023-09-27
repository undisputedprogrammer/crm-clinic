<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Center;
use Illuminate\Http\Request;
use App\Services\AgentService;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AgentController extends SmartController
{
    use HasMVConnector;
    private $connectorService;

    public function __construct(Request $request, AgentService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function index(Request $request)
    {
        $selectedCenter = $request->center;

        $agentsQuery = User::whereHas('roles', function($query){
            $query->where('name','agent');
        });

        if($selectedCenter != null && $selectedCenter != 'all'){
            $agentsQuery->whereHas('center', function($q) use($selectedCenter){
                $c = Center::find($selectedCenter);
                return $q->where('name', $c->name);
            });
        }

        $agents = $agentsQuery->paginate(10);

        $centers = Center::where('hospital_id',$request->user()->hospital_id)->get();

        return $this->buildResponse('pages.agents',compact('agents','centers','selectedCenter'));
    }

    public function store(Request $request)
    {
        $result = $this->connectorService->processAndStore($request);
        return response()->json($result);
    }

    public function update($id, Request $request)
    {
        $result = $this->connectorService->processAndUpdate($id, $request);
        return response()->json($result);
    }

    public function reset(Request $request){
        return $this->buildResponse('pages.reset-password',['request'=>$request]);
    }

    public function change(Request $request){
        $result = $this->connectorService->changePassword($request);
        return response()->json($result);
    }


}
