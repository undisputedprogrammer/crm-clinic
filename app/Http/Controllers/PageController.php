<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Message;
use App\Models\Followup;
use App\Models\Question;
use Illuminate\Http\Request;
use App\Services\PageService;
use Illuminate\Support\Facades\Auth;
use Ynotz\Metatags\Helpers\MetatagHelper;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class PageController extends SmartController
{
    private $pageService;

    public function __construct(Request $request, PageService $pageService)
    {
        parent::__construct($request);
        $this->pageService = $pageService;
    }

    public function overview()
    {
        $data = $this->pageService->getOverviewData();

        return $this->buildResponse('pages.overview', $data);
    }

    public function leadIndex(Request $request)
    {

        $data = $this->pageService->getLeads($request->user(),$request->selectedLeads);

        return $this->buildResponse('pages.leads', $data);

    }

    public function destroy(Request $request)
    {

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function home()
    {
        return redirect('/overview');
    }

    public function followUps(Request $request)
    {
        $data = $this->pageService->getFollowupData($request->user());

        return $this->buildResponse('pages.followups', $data);
    }

    public function searchIndex(Request $request)
    {
        $agents = User::whereHas('roles',function($q){
            $q->where('name','agent');
        })->get();
        return $this->buildResponse('pages.search', compact('agents'));
    }

    public function questionIndex(Request $request)
    {
        $questions = Question::orderBy('created_at', 'desc')->paginate(8);

        return $this->buildResponse('pages.manage-questions', compact('questions'));
    }
}
