<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Ynotz\EasyAdmin\Traits\HasMVConnector;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class DoctorController extends SmartController
{
    use HasMVConnector;
    private $connectorService;

    public function __construct(Request $request, DoctorService $service)
    {
        parent::__construct($request);
        $this->connectorService = $service;
    }

    public function index()
    {
        $doctors = Doctor::orderBy('id', 'desc')->paginate(10);
        return $this->buildResponse('pages.doctors',['doctors' => $doctors]);
    }
}
