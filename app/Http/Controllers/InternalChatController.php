<?php

namespace App\Http\Controllers;

use App\Services\InternalChatService;
use Illuminate\Http\Request;

class InternalChatController extends Controller
{
    private $icService;

    public function __construct(InternalChatService $icService)
    {
        $this->icService = $icService;
    }

    public function postMessage(Request $request)
    {
        
    }

    public function oldMessages(Request $request)
    {
        return response()->json(
            $this->icService->getMessages(
                $request->input('room_id'),
                $request->input('timestamp')
            )
        );
    }
}
