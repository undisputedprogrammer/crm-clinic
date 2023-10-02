<?php

namespace App\Http\Controllers;

use App\Models\Audit;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function fetch(Request $request)
    {
        $audits = Audit::where('user_id',$request->user_id)->get();
        return response($audits);
    }
}
