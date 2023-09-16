<?php

namespace App\Services;

use GuzzleHttp\Client;



class WhatsAppApiService
{
    public function message($request, $recipient)
    {
        $message = $request->message;
        $client = new Client();

        $url = 'https://control.msg91.com/api/v5/whatsapp/whatsapp-outbound-message/?integrated_number=918075473813&recipient_number=' . $recipient . '&content_type=text&text=' . $message;

        $response = $client->request('POST', $url, [
            'headers' => [
                'accept' => 'application/json',
                'authkey' => '405736ABdKIenjmHR6501a01aP1',
                'content-type' => 'application/json',
            ],
        ]);

        return $response;
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
}
