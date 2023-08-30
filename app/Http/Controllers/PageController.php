<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Followup;
use App\Models\Lead;
use App\Models\Question;
use Carbon\Carbon;
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
        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');
        $lpm = Lead::whereMonth('created_at',$currentMonth)->whereYear('created_at',$currentYear)->count();
        $ftm = Lead::where('followup_created',true)->whereMonth('created_at',$currentMonth)->whereYear('created_at',$currentYear)->count();
        $lcm = Lead::where('status','Converted')->whereMonth('created_at',$currentMonth)->whereYear('created_at',$currentYear)->count();
        $pf = Followup::whereHas('lead', function ($query) {
            $query->where('status','!=','Converted');
        })->where('next_followup_date',null)->count();
        return $this->buildResponse('pages.overview',compact('lpm','ftm','lcm','pf'));
    }

    public function leadIndex(Request $request){
        MetatagHelper::clearAllMeta();
        MetatagHelper::setTitle('Fresh leads - Clinic-crm');
        MetatagHelper::addMetatags(['description'=>'Customer relationship management system']);
        if($request->user()->hasRole('agent')) {
            $leads = Lead::where('status', '!=', 'Converted')->where('assigned_to', $request->user()->id)
            ->with(
                [
                'remarks' => function($q) {
                    return $q->orderBy('created_at', 'desc');
                },
                'answers'
                ])->paginate(10);
        }
        if($request->user()->hasRole('admin')){
            $leads = Lead::where('status', '!=', 'Converted')->with(['remarks','answers'])->paginate(10);
        }
        $doctors = Doctor::all();
        return $this->buildResponse('pages.leads',compact('leads','doctors'));
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
        if($request->user()->hasRole('admin')) {
            $followups = Followup::with(['lead','remarks'])->where('scheduled_date', '<=', date('Y-m-d'))->where('actual_date', null)->where('converted', null)->paginate(10);
        }
        if($request->user()->hasRole('agent')){
            $followups = Followup::with(['lead', 'remarks'])
            ->whereHas('lead', function ($query) {
                $query->where('assigned_to', Auth::id());
            })
            ->where('scheduled_date', '<=', date('Y-m-d'))
            ->where('actual_date', null)
            ->where('converted', null)
            ->paginate(10);
        }
        $doctors = Doctor::all();

        return $this->buildResponse('pages.followups', compact('followups','doctors'));
    }

    public function searchIndex(Request $request){
        MetatagHelper::clearAllMeta();
        MetatagHelper::setTitle('Follow ups - Clinic-crm');
        MetatagHelper::addMetatags(['description'=>'Customer relationship management system']);
        return $this->buildResponse('pages.search');
    }

    public function questionIndex(Request $request){
        MetatagHelper::clearAllMeta();
        MetatagHelper::setTitle('Follow ups - Clinic-crm');
        MetatagHelper::addMetatags(['description'=>'Customer relationship management system']);
        $questions = Question::orderBy('created_at', 'desc')->paginate(10);
        return $this->buildResponse('pages.manage-questions',compact('questions'));
    }
}
