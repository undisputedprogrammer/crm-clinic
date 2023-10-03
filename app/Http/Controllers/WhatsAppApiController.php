<?php

namespace App\Http\Controllers;

use App\Jobs\SendBulkMessage;
use Exception;
use App\Models\Chat;
use App\Models\Lead;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Message;
use App\Models\Followup;
use App\Models\UnreadMessages;
use Illuminate\Http\Request;
use App\Services\WhatsAppApiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Client\ResponseSequence;
use Ynotz\SmartPages\Http\Controllers\SmartController;

class WhatsAppApiController extends SmartController
{
    protected $connectorService;
    protected $request;
    public function __construct(Request $request, WhatsAppApiService $service)
    {
        parent::__construct($request);
        $this->request = $request;
        $this->connectorService = $service;
    }

    public function sent(Request $request)
    {
        // Fetching lead or follow up details
        if ($request->lead_id) {
            $lead = Lead::where('id', $request->lead_id)->with(['followups', 'appointment'])->get()->first();
            $recipient = $lead->phone;
        }
        if ($request->followup_id) {
            $followup = Followup::where('id', $request->followup_id)->with(['lead'])->get()->first();
            $recipient = $followup->lead->phone;
        }
        // Checking and sending non template message
        if ($request->template == 'custom' && $request->message != null) {

            $response = $this->connectorService->message($request, $recipient, $lead);

            return response()->json($response);
        }

        // Sending template message
        $template = Message::find($request->template);
        $params = json_decode($template->payload);

        if (count($params) == 0) {
            $components = [];
        } else {
            $components = array();
            array_push($components, array(
                'type' => 'body',
                'parameters' => array()
            ));
            foreach ($params as $param) {
                foreach ($param as $component => $data) {
                    $temp = explode('.', $data);
                    if ($temp[0] == 'Lead') {
                        array_shift($temp);
                        $data = $lead;
                        foreach ($temp as $i) {
                            if ($data[$i] == null) {
                                return response()->json(['status' => 'fail', 'message' => 'Invalid argument found']);
                            }
                            $data = $data[$i];
                        }

                        // return response($lead);
                    } elseif ($temp[0] == 'Followup') {
                        array_shift($temp);
                        $data = $followup;
                        foreach ($temp as $i) {
                            if ($data[$i] == null) {
                                return response()->json(['status' => 'fail', 'message' => 'Invalid argument found']);
                            }
                            $data = $data[$i];
                        }
                    }
                    // dd($temp);
                    array_push($components[0]['parameters'], array('type' => 'text', 'text' => $data));
                }
            }
        }


        // $recipient = $lead->phone;

        $payload = array(


            "name" => $template->template,
            "language" => array(
                "code" => "en",
                "policy" => "deterministic"
            ),
            "components" => json_encode($components),
        );


        $postfields = array(
            "integrated_number" => "918075473813",
            "lead_id" => $lead->id,
            "content_type" => "template",
            "type" => "template",
            "template" => $payload,
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $recipient
        );

        $json_postfields = json_encode($postfields);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v17.0/123563487508047/messages/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $json_postfields,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'authkey: 405736ABdKIenjmHR6501a01aP1',
                'Authorization: Bearer EAAMk25QApioBO6SvBdiMsr5HQPmivzZA5r50OwmoQqdEGVegEk4pgNIZAWJZAWg05WM1ZCqbod3TIuI3zUrXFVykJg2BkM5UVGha67SpVkDdeCz1vF9yg6Mb6JvFtY9GzsKtZBpKmMMMtZBo0otRnc5mlzszAHYtCUtfw21vwz086LuR1YaJdVYwthNTZBCgkFpp2ZA8R2I2TgX9'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($response, true);

        if(isset($data['error'])){
            return response()->json(['status'=>'fail','message'=>'Sorry! Could not send message']);
        }

        if ($data['messages'] != null) {
            info('Message is submitted');
            info($data);
            $message_params = $components[0]['parameters'];
            $placeholders = $this->connectorService->getVariables($template->body);
            $rendered_message = $this->connectorService->renderMessage($template->body, $placeholders, $message_params);
            info($rendered_message);
            $chat = Chat::create([
                'message' => $rendered_message,
                'direction' => 'Outbound',
                'lead_id' => $lead->id,
                'status' => 'submitted',
                'wamid' => $data['messages'][0]['id'],
                // 'template_id'=>$template->id
            ]);

            $data['status'] = 'success';
            $data['chat'] = $chat;
        }

        return response(json_encode($data), 200);
    }


    public function receive(Request $request)
    {
        info('webhook received:');
        info($request->all());
        info($request->header());

        // Storing inbound messages
        if (isset($request['entry'][0]['changes'][0]['value']['messages'][0]['from']))
         {
            $sender = $request['entry'][0]['changes'][0]['value']['messages'][0]['from'];

            $wamid = $request['entry'][0]['changes'][0]['value']['messages'][0]['id'];

            $timestamp = $request['entry'][0]['changes'][0]['value']['messages'][0]['timestamp'];

            $body = $request['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'];

            $lead = Lead::where('phone', $sender)
                ->orWhere('phone', '91' . $sender)->orWhere('phone', '+' . $sender)->get()->first();

            if ($lead == null) {
                $phone = $sender - 910000000000;
                $lead = Lead::where('phone', $phone)->get()->first();
            }

            // return response('lead is '.$lead->name);

            $lead_id = null;
            if ($lead != null) {
                $lead_id = $lead->id;
            }
            $chat = Chat::create([
                'message' => $body,
                'direction' => 'Inbound',
                'lead_id' => $lead_id,
                'status' => 'received',
                'wamid' => $wamid,
                'expiration_time' => $timestamp
            ]);

            // return response($chat);
            // Adding new inbound message to the unread messages table
            if ($lead != null) {
                $unread_message = UnreadMessages::where('lead_id', $lead->id)->latest()->get()->first();

                if ($unread_message != null) {

                    $unread_message->chat_id = $chat->id;
                    $unread_message->count = $unread_message->count + 1;
                    $unread_message->save();
                } else {
                    UnreadMessages::create([
                        'chat_id' => $chat->id,
                        'lead_id' => $lead->id,
                        'count' => 1
                    ]);
                }
            }

            return response()->json('ok');
        }

        // updating outbound messages
        $status = null;

        if (isset($request['entry'][0]['changes'][0]['value']['statuses'][0]['status'])) {

            $status = $request['entry'][0]['changes'][0]['value']['statuses'][0]['status'];
            $wamid = $request['entry'][0]['changes'][0]['value']['statuses'][0]['id'];

            $chat = Chat::where('wamid', $wamid)->get()->first();
            if ($status == "sent") {
                $chat->status = 'sent';
                $chat->save();
                return true;
            } elseif ($status == "delivered") {
                $chat->status = 'delivered';
                $chat->save();
                return true;
            } elseif ($status == 'read') {
                $chat->status = 'read';
                $chat->save();
                return true;
            }

            return false;
        }
    }

    public function getChats(Request $request)
    {
        $chats = Chat::where('lead_id', $request->id)->get();
        $expiry = Chat::where('lead_id', $request->id)
        ->where('direction', 'Inbound')
        ->orderBy('created_at', 'desc')
        ->first();
        if($expiry != null){
            $expiration_time = $expiry->expiration_time;
        }else{
            $expiration_time = null;
        }
        return response()->json(['chats' => $chats, 'expiration_time' => $expiration_time]);
    }

    public function unread(Request $request)
    {
        // $user = User::where('id',$request->user_id)->with(['leads'])->get();
        $user = User::find($request->user_id);
        $leadIDs = $user->leads->pluck('id')->toArray();
        return response($leadIDs);
    }

    public function poll(Request $request)
    {
        $user = User::find($request->user_id);

        if ($user->hasRole('agent')) {
            $leadIDs = $user->leads->pluck('id')->toArray();
            $unread_messages = UnreadMessages::whereIn('lead_id', $leadIDs)->get();
        }
        if ($user->hasRole('admin')) {
            $unread_messages = UnreadMessages::all();
        }
        if ($user->hasRole('admin')) {
            $msgsQuery = Chat::where('direction','Inbound')->where('status','received')->whereHas('lead', function ($query) {
                $query->where('hospital_id', auth()->user()->hospital_id);
            });
        } else {
            $msgsQuery = Chat::whereIn('lead_id', $leadIDs)->where('direction','Inbound')->where('status','received')->with('lead');
        }
        if ($request->latest != null) {
            $latest = Chat::find($request->latest);
            $msgsQuery->where('created_at', '>', $latest->created_at);

            /**
             * removed old logic to find messages for admin
             */
            // if ($user->hasRole('admin')) {
            //     $new_messages = Chat::where('direction','Inbound')->where('status','received')->where('created_at', '>', $latest->created_at)->with('lead')->get();
            // } else {
            //     $new_messages = Chat::whereIn('lead_id', $leadIDs)->where('direction','Inbound')->where('status','received')->where('created_at', '>', $latest->created_at)->with('lead')->latest()->get();
            // }
        }
        $new_messages = $msgsQuery->latest()->get();
        $unread = [];

        if ($unread_messages != null && count($unread_messages) > 0) {
            foreach ($unread_messages as $msg) {
                array_push($unread, array($msg->lead_id => $msg));
            }
        }

        // return response($unread_messages);
        if ($new_messages != null && count($new_messages) > 0) {
            return response()->json(['status' => true, 'new_messages' => $new_messages, 'unread_messages' => $unread]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function index(Request $request)
    {
        $user_ids = $request->user()->leads->pluck('id')->toArray();
        // dd($user_ids);
        $q = Lead::has('chats')->with('chats')->where('hospital_id',$request->user()->hospital_id);

        if ($request->user()->hasRole('agent')) {
            $q->where('assigned_to', $request->user()->id);
            $latest = Chat::whereIn('lead_id', $user_ids)->latest()->get()->first();
        } else {
            $latest = Chat::with(['lead' => function ($query) {
                return $query->where('hospital_id', auth()->user()->hospital_id);
            }])->latest()->get()->first();
        }

        $leads = $q->get();
        $templates = Message::all();
        // dd(['leads'=>$leads,'latest'=>$latest]);
        return $this->buildResponse('pages.messenger', compact('leads', 'templates', 'latest'));
    }

    public function bulkMessage(Request $request)
    {
        $numbers = json_decode(json_encode($request->numbers), true);

        $lead_ids = array_keys($numbers);

        $template = Message::find($request->template);

        foreach ($lead_ids as $lead_id) {
            SendBulkMessage::dispatch($lead_id, $template);
        }

        return response()->json(['numbers' => array_keys($numbers), 'template' => $template]);
    }

    public function verify()
    {
        $token = $this->request->input('hub_verify_token');
        $challenge = null;
        if ($token == config('appSettings.webhook_token')) {
            $challenge = $this->request->input('hub_challenge');
        }

        return $challenge;
    }

    public function fetchLatest(Request $request){

        if ($request->user()->hasRole('agent')) {
            $user_ids = $request->user()->leads->pluck('id')->toArray();
            $latest = Chat::whereIn('lead_id', $user_ids)->where('direction','Inbound')->where('status','received')->latest()->get()->first();
            $unread_messages_count = Chat::whereIn('lead_id', $user_ids)->where('direction','Inbound')->where('status','received')->latest()->get()->count();
        } else {
            $latest = Chat::where('lead_id', '!=', null)->where('direction','Inbound')->where('status','received')->latest()->get()->first();
            $unread_messages_count = Chat::where('lead_id', '!=', null)->where('direction','Inbound')->where('status','received')->get()->count();
        }
        if($latest == null){
            return response()->json(['latest'=>null,'unread_message_count'=>$unread_messages_count]);
        }else{
            return response()->json(['latest'=>$latest->id, 'unread_message_count'=>$unread_messages_count]);
        }
    }

    public function markRead(Request $request){
        $chats = Chat::where('lead_id',$request->lead_id)->where('direction','Inbound')->where('status','received')->get();

        if($chats != null && count($chats) > 0){
            foreach($chats as $chat){
                $chat->status='read';
                $chat->save();
            }
        }

        return response(true, 200);
    }
}
