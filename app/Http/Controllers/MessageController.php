<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function message(Request $request){

        return response()->json(['success'=>true, 'message'=>'Message sent to '.$request->lead_name]);

    }
}
