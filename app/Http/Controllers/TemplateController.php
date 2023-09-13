<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Services\MessageService;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class TemplateController extends SmartController
{
    protected $connectorService;
    protected $request;
    public function __construct(Request $request, MessageService $service)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->connectorService = $service;
    }

    public function index()
    {
        $messages = Message::orderBy('id', 'desc')->paginate(10);

        return $this->buildResponse('pages.template-new',compact('messages'));
    }

    public function store(){
        $params = $this->request->all();
        $vars = [];
        foreach($params as $param => $name){
            if(substr($param,0,3) == 'var' && $name != null){
                array_push($vars,[$param=>$name]);
            }
        }
        return response()->json(['success'=>true, 'message'=>'Template Created !!','params'=>$vars]);
    }

    public function reassign(Request $request){
        if($request->filter != null && $request->filter != 0){
            $leads = Lead::where('assigned_to',$request->filter)->paginate(10);
        }
        else{
            $leads = Lead::paginate(10);
        }

        $agents = User::whereHas('roles',function($q){
            $q->where('name','agent');
        })->get();

        return $this->buildResponse('pages.reassign-lead', compact('leads','agents'));
    }

    public function assign(Request $request){
        $i=0;
        $leads = explode(",",$request->selectedLeads);
        foreach($leads as $lead){
            $l = Lead::find($lead);
            $l->assigned_to = $request->agent;
            $l->save();
        }
        return response()->json(['success'=>true, 'message'=>'Successfully assigned '.$i.' leads','agent'=>$request->agent,'leads'=>$request->selectedLeads]);
    }
}
