<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Followup;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AppointmentController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }


}
