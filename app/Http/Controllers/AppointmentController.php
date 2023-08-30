<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class AppointmentController extends SmartController
{
    public function __construct(Request $request, AppointmentService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function index()
    {
        $appointments = Appointment::orderBy('id', 'desc')->paginate(10);
        return $this->buildResponse('pages.appointments',['appointments' => $appointments]);
    }
}
