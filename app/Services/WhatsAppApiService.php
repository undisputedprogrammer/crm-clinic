<?php

namespace App\Services;

use App\Models\Chat;
use App\Models\Lead;
use GuzzleHttp\Client;
use App\Models\Followup;



class WhatsAppApiService
{
    public function message($request, $recipient, $lead)
    {
        $message = $request->message;
        $client = new Client();

        // $url = 'https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/?integrated_number=918075473813&recipient_number=' . $recipient . '&content_type=text&text=' . $message;

        // $response = $client->request('POST', $url, [
        //     'headers' => [
        //         'accept' => 'application/json',
        //         'authkey' => '405736ABdKIenjmHR6501a01aP1',
        //         'content-type' => 'application/json',
        //     ],
        // ]);
        $postfields = array(
            "messaging_product"=> "whatsapp",
            "recipient_type"=> "individual",
            "to"=> $recipient,
            "type"=>"text",
            "text"=>array(
                "preview_url"=> false,
                "body"=>$message
            )
        );
        $json_postfields = json_encode($postfields);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://graph.facebook.com/v18.0/123563487508047/messages',
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
            return ['status'=>'fail','message'=>'Cannot sent custom message'];
        }
        if($data['messages'] != null){
            $chat = Chat::create([
                'message'=>$message,
                'direction'=>'Outbound',
                'lead_id'=>$lead->id,
                'status'=>'submitted',
                'wamid'=>$data['messages'][0]['id'],

            ]);
            $data['status'] = 'success';
            $data['chat'] = $chat;
            return $data;
        }else{
            return ['status'=>'fail','message'=>'Cannot sent message'];
        }
    }

    public function gettemplate($template_name)
    {
        $integrated_number = config('credentials.integrated_number');
        $authkeky = config('credentials.authkey');
        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://control.msg91.com/api/v5/whatsapp/get-template-client/' . $integrated_number . '?template_name=' . $template_name, [
            'headers' => [
                'accept' => 'application/json',
                'authkey' => $authkeky,
            ],
        ]);

        $r = json_decode($response->getBody(), true);
        return $r;
    }

    public function getVariables($template_body)
    {
        $pattern = '/{{(.*?)}}/';

        preg_match_all($pattern, $template_body, $matches);

        $placeholders = $matches[0];

        return $placeholders;
    }

    public function getParameters($content)
    {
        $data = json_decode(json_encode($content), true);

        if ($data && isset($data['template']['components'])) {
            $components = json_decode($data['template']['components'], true);

            if ($components[0]['type'] === 'body' && isset($components[0]['parameters'])) {
                $parameters = $components[0]['parameters'];


                return $parameters;
            }
        }
    }

    public function renderMessage($template_body, $vars, $parameters)
    {
        $placeholderMap = [];

        foreach ($vars as $index => $var) {
            $placeholderMap[$var] = $parameters[$index]['text'];
        }

        $message = str_replace(array_keys($placeholderMap), array_values($placeholderMap), $template_body);

        return $message;
    }


    public static function bulkMessage($lead_id, $template){

        // Fetching lead or follow up details
        if ($lead_id) {
            $lead = Lead::where('id', $lead_id)->with(['followups', 'appointment'])->get()->first();
            $recipient = $lead->phone;
        }



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
                        $followup = Followup::where('lead_id',$lead_id)->with('lead')->get()->first();
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
        $serviceObject = new WhatsAppApiService;
        if ($data['messages'] != null) {
            info('Message is submitted');
            info($data);
            $message_params = $components[0]['parameters'];
            $placeholders = $serviceObject->getVariables($template->body);
            $rendered_message = $serviceObject->renderMessage($template->body, $placeholders, $message_params);
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
}
