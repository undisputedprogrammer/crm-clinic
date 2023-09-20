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

            $import = new LeadsImport($headings[0][0]);
            Excel::import($import, request()->file('sheet'));

            $msg = "{$import->getImportedCount()} of {$import->getTotalCount()} leads imported";

            return response()->json([
                'success' => true,
                'message' => $msg,
                'headings'=>$headings[0][0],
            ]);
        }
        else{

            return response()->json([
                'success' => false,
                'message' => 'Unprocessable file'
            ]);

        }
    }
}
