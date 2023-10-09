<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Center;
use App\Models\Doctor;
use App\Models\Journal;
use App\Models\Message;
use App\Models\Followup;
use App\Models\Hospital;
use Illuminate\Support\Facades\Auth;


class PageService
{

    public function getLeads($user, $selectedLeads, $selectedCenter, $search, $status, $is_valid, $is_genuine)
    {
        if($search != null){
            $leadsQuery = Lead::where('hospital_id', $user->hospital_id)->where('name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%');

            $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
                return $query->where('assigned_to', $user->id);
            });

            return $this->returnLeads($user,$selectedLeads,$selectedCenter,$leadsQuery,$status);
        }


        if($status != null && $status != 'none')
        {
            if($status == 'all'){
                $leadsQuery = Lead::where('hospital_id', $user->hospital_id)->with([
                    'remarks' => function ($q) {
                        return $q->orderBy('created_at', 'desc');
                    },
                    'appointment'
                ]);
            }
            else{
                $leadsQuery = Lead::where('hospital_id', $user->hospital_id)->where('status', $status)->with([
                    'remarks' => function ($q) {
                        return $q->orderBy('created_at', 'desc');
                    },
                    'appointment'
                ]);
            }

        }
        else{
            $leadsQuery = Lead::where('followup_created', false)->where('hospital_id', $user->hospital_id)->where('status', '!=', 'Consulted')->with([
                'remarks' => function ($q) {
                    return $q->orderBy('created_at', 'desc');
                },
                'appointment'
            ]);
        }

        $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
            return $query->where('assigned_to', $user->id);
        });

        if($selectedCenter != null && $selectedCenter != 'all'){
            $leadsQuery->where('center_id',$selectedCenter);
        }

        if($is_valid != null){
            if($is_valid == 'true'){
                $leadsQuery->where('is_valid', true);
            }else{
                $leadsQuery->where('is_valid', false);
            }

        }

        if($is_genuine != null){
            if($is_genuine == 'true'){
                $leadsQuery->where('is_genuine', true);
            }else{
                $leadsQuery->where('is_genuine', false);
            }
        }

        $leads = $leadsQuery->paginate(10);

        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        if($selectedLeads != null){
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads','centers','selectedCenter','status', 'is_valid', 'is_genuine');
        }
        else{
            return compact('leads', 'doctors', 'messageTemplates','centers','selectedCenter','status', 'is_valid', 'is_genuine');
        }

    }

    public function returnLeads($user, $selectedLeads, $selectedCenter, $leadsQuery, $status)
    {
        $leads = $leadsQuery->paginate(10);
        $doctors = Doctor::all();
        $messageTemplates = Message::all();
        $centers = Center::where('hospital_id',$user->hospital_id)->get();

        if($selectedLeads != null){
            return compact('leads', 'doctors', 'messageTemplates','selectedLeads','centers','selectedCenter','status');
        }
        else{
            return compact('leads', 'doctors', 'messageTemplates','centers','selectedCenter','status');
        }
    }

    public function getOverviewData()
    {

        $now = Carbon::now();

        $date = $now->format('Y-m-j');

        $currentMonth = $now->format('m');

        $currentYear = $now->format('Y');

        $hospital = auth()->user()->hospital;
        $hospitals = [$hospital];
        $centers = $hospitals[0]->centers;
        /**
         * @var User
         */
        $authUser = auth()->user();

        if($authUser->hasRole('admin')) {
            $lpm = Lead::forHospital($hospital->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $ftm = Lead::forHospital($hospital->id)->where('followup_created', true)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $lcm = Lead::forHospital($hospital->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();


            $pf = Followup::whereHas('lead', function ($query) {
                $query->where('status', '!=', 'Appointment Fixed');
            })->where('next_followup_date', null)
            ->where('consulted',null)->count();
        } else {
            $lpm = Lead::forAgent($authUser->id)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $ftm = Lead::forAgent($authUser->id)->where('followup_created', true)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();

            $lcm = Lead::forAgent($authUser->id)->where('status', 'Consulted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();


            $pf = Followup::whereHas('lead', function ($query) use($authUser){
                $query->where('status', '!=', 'Appointment Fixed')
                ->where('assigned_to', $authUser->id);
            })->where('next_followup_date', null)
            ->where('consulted',null)->count();
        }
        $journal = Journal::where('user_id',auth()->user()->id)->where('date',$date)->get()->first();
        // $process_chart_data = $this->getProcessChartData($currentMonth);
        $process_chart_data = json_encode($this->getProcessChartData($currentMonth));
        $valid_chart_data = json_encode($this->getValidChartData($currentMonth));
        $genuine_chart_data = json_encode($this->getGenuineChartData($currentMonth));
        return compact('lpm', 'ftm', 'lcm', 'pf', 'hospitals', 'centers','journal','process_chart_data','valid_chart_data','genuine_chart_data');
    }

    public function getProcessChartData($currentMonth){
        $process_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereMonth('created_at',$currentMonth);
        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }
        $newQuery = clone $baseQuery;
        $process_chart_data['unprocessed_leads'] = $newQuery->where('status','Created')->where('followup_created',false)->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['followed_up_leads'] = $newQuery->where('status','Follow-up Started')->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['appointments_created'] = $newQuery->where('status','Appointment Fixed')->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['consulted'] =  $newQuery->where('status','Consulted')->count();

        $newQuery = clone $baseQuery;
        $process_chart_data['closed'] =$newQuery->where('status','Closed')->count();

        return $process_chart_data;
    }

    public function getValidChartData($currentMonth){
        $valid_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereMonth('created_at',$currentMonth);
        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }

        $newQuery = clone $baseQuery;
        $valid_chart_data['valid_leads'] = $newQuery->where('is_valid',true)->count();

        $newQuery = clone $baseQuery;
        $valid_chart_data['invalid_leads'] = $newQuery->where('is_valid',false)->count();

        return $valid_chart_data;
    }

    public function getGenuineChartData($currentMonth){
        $genuine_chart_data = [];
        $hospitalID = auth()->user()->hospital_id;
        $user = Auth::user();
        $baseQuery = Lead::forHospital($hospitalID)->whereMonth('created_at',$currentMonth);
        if($user->hasRole('agent')){
            $baseQuery->where('assigned_to',$user->id);
        }

        $newQuery = clone $baseQuery;
        $genuine_chart_data['genuine_leads'] = $newQuery->where('is_genuine',true)->count();

        $newQuery = clone $baseQuery;
        $genuine_chart_data['false_leads'] = $newQuery->where('is_genuine',false)->count();

        return $genuine_chart_data;
    }

    public function getFollowupData($user, $selectedCenter)
    {

        $followupsQuery = Followup::whereHas('lead', function ($qr) use ($user) {
            return $qr->where('hospital_id',$user->hospital_id);
        })->with(['lead'=>function($q) use($user){
            return $q->with(['appointment'=>function($qr){
                return $qr->with('doctor');
            }]);
        }, 'remarks'])
            ->where('scheduled_date', '<=', date('Y-m-d'))
            ->where('actual_date', null);

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
