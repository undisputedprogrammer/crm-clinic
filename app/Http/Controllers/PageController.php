<?php

namespace App\Http\Controllers;

use App\Models\Followup;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ynotz\Metatags\Helpers\MetatagHelper;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class PageController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }
    public function overview()
    {
        MetatagHelper::clearAllMeta();
        MetatagHelper::setTitle('Overview - Clinic-crm');
        MetatagHelper::addMetatags(['description'=>'Customer relationship management system']);
        return $this->buildResponse('pages.overview');
    }

    public function leadIndex(){
        MetatagHelper::clearAllMeta();
        MetatagHelper::setTitle('Fresh leads - Clinic-crm');
        MetatagHelper::addMetatags(['description'=>'Customer relationship management system']);
        $leads = Lead::paginate(10);
        return $this->buildResponse('pages.leads',compact('leads'));
    }

    public function destroy(Request $request){

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function home(){
        return redirect('/overview');
    }

    public function followUps(Request $request){
        MetatagHelper::clearAllMeta();
        MetatagHelper::setTitle('Follow ups - Clinic-crm');
        MetatagHelper::addMetatags(['description'=>'Customer relationship management system']);
        $followups = Followup::with(['lead','remarks'])->where('scheduled_date','<=',date('Y-m-d'))->where('actual_date',null)->paginate(10);
        return $this->buildResponse('pages.followups', compact('followups'));
    }

    public function searchIndex(Request $request){
        MetatagHelper::clearAllMeta();
        MetatagHelper::setTitle('Follow ups - Clinic-crm');
        MetatagHelper::addMetatags(['description'=>'Customer relationship management system']);
        return $this->buildResponse('pages.search');
    }
}
