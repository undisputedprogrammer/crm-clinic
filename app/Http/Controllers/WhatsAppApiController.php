<?php

namespace App\Http\Controllers;

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

            $response = $this->connectorService->message($request, $recipient);

            return response($response->getBody(), 200);
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

        // return response($components);
        // $recipient = $lead->phone;

        $payload = array(
            "to" => $recipient,
            "type" => "template",
            "template" => array(
                "name" => $template->template,
                "language" => array(
                    "code" => "en",
                    "policy" => "deterministic"
                ),
                "components" => json_encode($components)
            ),
            "messaging_product" => "whatsapp"
        );


        $postfields = array(
            "integrated_number" => "918075473813",
            "lead_id" => $lead->id,
            "content_type" => "template",
            "payload" => $payload,
            "messaging_product" => "whatsapp"

        );

        $json_postfields = json_encode($postfields);

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/',
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
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);



        // return redirect('/leads');
        return response($response, 200);
    }


    public function recieve(Request $request)
    {

        // Storing inbound messages
        if ($request->sender != null) {

            $lead = Lead::where('phone', $request->sender)->get()->first();

            if ($lead == null) {

                $phone = 910000000000 + $request->sender;
                $lead = Lead::where('phone', $phone)->get()->first();
            }

            if ($lead == null) {

                $phone = $request->sender - 910000000000;
                $lead = Lead::where('phone', $phone)->get()->first();
            }

            // return response('lead is '.$lead->name);

            $lead_id = null;
            if ($lead != null) {
                $lead_id = $lead->id;
            }
            $chat = Chat::create([
                'message' => $request->text,
                'direction' => 'Inbound',
                'lead_id' => $lead_id
            ]);

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

            return response([$chat, $lead_id]);
        }

        // Storing outbound messages
        if ($request->status == "sent") {

            if (array_key_exists('text', $request->content)) {
                $lead = Lead::where('phone', $request->customer_number)->get()->first();
                if ($lead != null) {
                    Chat::create([
                        'message' => $request->content['text'],
                        'direction' => 'Outbound',
                        'lead_id' => $lead->id
                    ]);
                } else {
                    Chat::create([
                        'message' => $request->content['text'],
                        'direction' => 'Outbound',
                        'lead_id' => null
                    ]);
                }
                return response('Message sent to ' . $lead->name);
            } else {
                $parameters = $this->connectorService->getParameters($request->content);

                $template = $this->connectorService->gettemplate($request->content['template']['name']);

                $template_body = $template['data'][0]['languages'][0]['code'][0]['text'];

                $vars = $this->connectorService->getVariables($template_body);

                $message = $this->connectorService->renderMessage($template_body, $vars, $parameters);

                $lead = Lead::where('phone', $request->customer_number)->get()->first();

                if ($lead != null) {
                    $chat = Chat::create([
                        'message' => $message,
                        'direction' => 'Outbound',
                        'lead_id' => $lead->id
                    ]);
                } else {
                    $chat = Chat::create([
                        'message' => $message,
                        'direction' => 'Outbound',
                        'lead_id' => null
                    ]);
                }

                return response(['vars' => $vars, 'parameters' => $parameters, 'message' => $template_body, 'chat' => $chat]);
            }

            $parameters = $this->connectorService->getParameters($request->content);
            return response($parameters);

            $lead = Lead::where('phone', $request->customer_number)->get()->first();

            $template = $this->connectorService->gettemplate($request->content['template']['name']);

            $template_body = $template['data'][0]['languages'][0]['code'][0]['text'];

            $vars = $this->connectorService->getVariables($template_body);

            return response($vars);
            if ($lead != null) {
                Chat::create([
                    'message' => $request->content->text,
                    'direction' => 'Outbound',
                    'lead_id' => $lead->id
                ]);
            } else {
                Chat::create([
                    'message' => $request->content->text,
                    'direction' => 'Outbound',
                    'lead_id' => null
                ]);
            }

            return response('message sent to ' . $lead->name . ' and recorded');
        }
    }

    public function getChats(Request $request)
    {
        $chats = Chat::where('lead_id', $request->id)->get();
        $lead = Lead::find($request->id);
        return response()->json(['chats' => $chats, 'lead' => $lead]);
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

        if ($request->latest) {
            $latest = Chat::find($request->latest);
            if ($user->hasRole('admin')) {
                $new_messages = Chat::where('created_at', '>', $latest->created_at)->get();
            } else {
                $new_messages = Chat::whereIn('lead_id',$leadIDs)->where('created_at', '>', $latest->created_at)->latest()->get();
            }
        }

        $unread = [];

        if($unread_messages != null && count($unread_messages) > 0){
            foreach($unread_messages as $msg){
                array_push($unread, array($msg->lead_id => $msg));
            }
        }

        // return response($unread_messages);
        if ($new_messages != null && count($new_messages) > 0) {
            return response()->json(['status' => true, 'new_messages' => $new_messages, 'unread_messages'=> $unread]);
        } else {
            return response()->json(['status' => false]);
        }
    }

    public function index(Request $request)
    {
        $user_ids = $request->user()->leads->pluck('id')->toArray();
        // dd($user_ids);
        $q = Lead::has('chats')->with('chats');

        if ($request->user()->hasRole('agent')) {
            $q->where('assigned_to', $request->user()->id);
            $latest = Chat::whereIn('lead_id', $user_ids)->latest()->get()->first();
        } else {
            $latest = Chat::where('lead_id', '!=', null)->latest()->get()->fisrt();
        }

        $leads = $q->get();
        $templates = Message::all();
        // dd(['leads'=>$leads,'latest'=>$latest]);
        return view('pages.messenger', compact('leads', 'templates', 'latest'));
    }
}
