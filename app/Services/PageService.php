<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Followup;
use App\Models\Hospital;
use Illuminate\Support\Facades\Auth;


class PageService
{

    public function getLeads($user, $selectedLeads, $selectedCenter)
    {

        $leadsQuery = Lead::where('hospital_id', $user->hospital_id)->where('status', '!=', 'Consulted')->with([
            'remarks' => function ($q) {
                return $q->orderBy('created_at', 'desc');
            },
            'appointment'
        ]);

        $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
            return $query->where('assigned_to', $user->id);

        });

        if($selectedCenter != null && $selectedCenter != 'all'){
            $leadsQuery->where('center_id',$selectedCenter);
        }

        $leads = $leadsQuery->paginate(10);

        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        if($selectedLeads != null){
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads','centers','selectedCenter');
        }else{
            return compact('leads', 'doctors', 'messageTemplates','centers','selectedCenter');
        }

    }

    public function getOverviewData()
    {

        $now = Carbon::now();

        $currentMonth = $now->format('m');

        $currentYear = $now->format('Y');

        $hospital = auth()->user()->hospital;
        $hospitals = [$hospital];
        $centers = $hospitals[0]->centers;
        $lpm = Lead::forHospital($hospital->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

        $ftm = Lead::forHospital($hospital->id)->where('followup_created', true)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

        $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();


        $pf = Followup::whereHas('lead', function ($query) {
            $query->where('status', '!=', 'Converted');
        })->where('next_followup_date', null)
        ->where('consulted',null)->count();

        return compact('lpm', 'ftm', 'lcm', 'pf', 'hospitals', 'centers');
    }

    public function getFollowupData($user, $selectedCenter)
    {

        $followupsQuery = Followup::whereHas('lead', function ($qr) use ($user) {
            return $qr->where('hospital_id',$user->hospital_id);
        })->with(['lead'=>function($q) use($user){
            return $q->with('appointment');
        }, 'remarks'])
            ->where('scheduled_date', '<=', date('Y-m-d'))
            ->where('actual_date', null)
            ->where('consulted', null);

        if ($user->hasRole('agent')) {
            $followupsQuery->whereHas('lead', function ($query) use ($user) {
                $query->where('assigned_to', $user->id);
            });
        }

        if($selectedCenter != null && $selectedCenter != 'all' && $user->hasRole('admin')){
            $followupsQuery->whereHas('lead', function ($qry) use($selectedCenter) {
                return $qry->where('center_id', $selectedCenter);
            });
        }

        $followups = $followupsQuery->paginate(10);
        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        return compact('followups', 'doctors','messageTemplates','centers','selectedCenter');
    }
}
