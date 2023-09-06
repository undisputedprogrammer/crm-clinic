<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        $agents = User::whereHas('roles', function($query){
            $query->where('name','agent');
        })->paginate(10);
        return $this->buildResponse('pages.agents',compact('agents'));
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
