<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Chat;
use App\Models\Lead;
use GuzzleHttp\Client;
use App\Models\Message;
use App\Models\Followup;
use Illuminate\Http\Request;
use App\Services\WhatsAppApiService;
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
        $recipient = 918137033348;

        // Fetching lead or follow up details
        if ($request->lead_id) {
            $lead = Lead::where('id', $request->lead_id)->with(['followups', 'appointment'])->get()->first();
        }
        if ($request->followup_id) {
            $followup = Followup::where('id', $request->followup_id)->with(['lead'])->get()->first();
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
        $recipient = 8137033348;

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


        if ($request->sender != null) {

            $lead = Lead::where('phone', $request->sender)->get()->first();

            if($lead == null){
                $phone = 910000000000 + $request->sender;
                $lead = Lead::where('phone', $phone)->get()->first();
            }

            $lead_id = null;
            if ($lead != null) {
                $lead_id = $lead->id;
            }
            $chat = Chat::create([
                'message' => $request->text,
                'direction' => 'Inbound',
                'lead_id' => $lead_id
            ]);

            return response([$chat, $lead_id]);
        }

        if ($request->status == "sent") {

            if(array_key_exists('text',$request->content))
            {
                $lead = Lead::where('phone', $request->customer_number)->get()->first();
                if ($lead != null) {
                    Chat::create([
                        'message' => $request->content['text'],
                        'direction' => 'Outbound',
                        'lead_id' => $lead->id
                    ]);
                }
                else{
                    Chat::create([
                        'message' => $request->content['text'],
                        'direction' => 'Outbound',
                        'lead_id' => null
                    ]);
                }
                return response('Message sent to '.$lead->name);
            }
            else{
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
                }
                else{
                    $chat = Chat::create([
                        'message' => $message,
                        'direction' => 'Outbound',
                        'lead_id' => null
                    ]);
                }

                return response(['vars'=>$vars,'parameters'=>$parameters,'message'=>$template_body,'chat'=>$chat]);
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
            }
            else{
                Chat::create([
                    'message' => $request->content->text,
                    'direction' => 'Outbound',
                    'lead_id' => null
                ]);
            }

            return response('message sent to ' . $lead->name . ' and recorded');
        }
    }

    public function getChats(Request $request){
        $chats = Chat::where('lead_id',$request->id)->get();
        $lead = Lead::find($request->id);
        return response()->json(['chats'=>$chats,'lead'=>$lead]);
    }
}
