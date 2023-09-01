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

    public function getLeads($user){

        $leadsQuery = Lead::where('status', '!=', 'Converted');

        $leadsQuery->when($user->hasRole('agent'), function ($query) use ($user) {
            return $query->where('assigned_to', $user->id)
                ->with([
                    'remarks' => function ($q) {
                        return $q->orderBy('created_at', 'desc');
                    },
                    'answers',
                ]);
        });

        $leads = $leadsQuery->paginate(10);

        $doctors = Doctor::all();
        $messageTemplates = Message::all();

        return compact('leads', 'doctors', 'messageTemplates');
    }

    public function getOverviewData(){

        $now = Carbon::now();
        $currentMonth = $now->format('m');
        $currentYear = $now->format('Y');
        $lpm = Lead::whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $ftm = Lead::where('followup_created', true)->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $lcm = Lead::where('status', 'Converted')->whereMonth('created_at', $currentMonth)->whereYear('created_at', $currentYear)->count();
        $pf = Followup::whereHas('lead', function ($query) {
            $query->where('status', '!=', 'Converted');
        })->where('next_followup_date', null)->count();
        return compact('lpm', 'ftm', 'lcm', 'pf');

    }

    public function getFollowupData($user){

        if ($user->hasRole('admin')) {
            $followups = Followup::with(['lead', 'remarks'])->where('scheduled_date', '<=', date('Y-m-d'))->where('actual_date', null)->where('converted', null)->paginate(10);
        }
        if ($user->hasRole('agent')) {
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

        return compact('followups', 'doctors');
    }

}
?>
