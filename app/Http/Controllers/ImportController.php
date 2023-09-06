<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use Illuminate\Http\Request;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class ImportController extends SmartController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }



    public function importLead(Request $request)
    {

        if($request->file('sheet')) {

            $headings = (new HeadingRowImport)->toArray($request->file('sheet'));

            Excel::import(new LeadsImport($headings[0][0]), request()->file('sheet'));

            return response()->json(['success' => true, 'message' => 'Success, Leads imported','headings'=>$headings[0][0]]);
        }
        else{

            return response()->json(['success' => false, 'message' => 'Unprocessable file']);

        }
    }
}
