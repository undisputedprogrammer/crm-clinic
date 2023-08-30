<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\AppointmentService;
use Carbon\Carbon;
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
        $query = Appointment::orderBy('appointment_date', 'asc');
        if (isset($this->request->from)) {
            $query->where('appointment_date', '>=', $this->request->from);
        }
        if (isset($this->request->to)) {
            $query->where('appointment_date', '<=', $this->request->to);
        }
        $appointments = $query->paginate(10);
        return $this->buildResponse('pages.appointments',['appointments' => $appointments]);
    }
}
