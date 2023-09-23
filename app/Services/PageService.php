<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Followup;
use Illuminate\Support\Facades\Auth;


class PageService
{

    public function getLeads($user, $selectedLeads)
    {

        $leadsQuery = Lead::where('status', '!=', 'Consulted')->with([
            'remarks' => function ($q) {
                return $q->orderBy('created_at', 'desc');
            },
            'answers',
            'appointment'
        ]);;

        $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
            return $query->where('assigned_to', $user->id);

        });

        $leads = $leadsQuery->paginate(10);

        $doctors = Doctor::all();
        $messageTemplates = Message::all();

        if($selectedLeads != null){
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads');
        }else{
            return compact('leads', 'doctors', 'messageTemplates');
        }

    }

    public function getOverviewData()
    {

        $now = Carbon::now();

        $currentMonth = $now->format('m');

        $currentYear = $now->format('Y');

        $lpm = Lead::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

        $ftm = Lead::where('followup_created', true)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

        $lcm = Lead::where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

        $pf = Followup::whereHas('lead', function ($query) {
            $query->where('status', '!=', 'Converted');
        })->where('next_followup_date', null)
        ->where('consulted',null)->count();

        return compact('lpm', 'ftm', 'lcm', 'pf');
    }

    public function getFollowupData($user)
    {

        $followupsQuery = Followup::with(['lead'=>function($q){
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

        $followups = $followupsQuery->paginate(10);
        $doctors = Doctor::all();
        $messageTemplates = Message::all();

        return compact('followups', 'doctors','messageTemplates');
    }
}
