<?php

namespace Trin4ik\DevinoApi;

use GuzzleHttp\Client as HttpClient;
use Trin4ik\DevinoApi\Exceptions\ErrorSendNotification;
use Illuminate\Support\Facades\DB;

class DevinoApi
{
    protected $client;
    protected $url;
    protected $login;
    protected $password;
    protected $sender;
    protected $validity;
    protected $priority;
    protected $callbackUrl;

    public function __construct()
    {
        $this->login = config('devino.auth_plain.login');
        $this->password = config('devino.auth_plain.password');
        $this->sender = config('devino.sender');
        $this->url = config('devino.url');
        $this->validity = config('devino.validity');
        $this->priority = config('devino.priority');
        $this->callbackUrl = config('devino.callback_url');

        $this->client = new HttpClient([
            'timeout' => 5,
            'connect_timeout' => 5,
        ]);
    }

    public function send($params)
    {
        $headers = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
        $post = [
            'Login'                 => $this->login,
            'Password'              => $this->password,
            'SourceAddress'         => !empty($params['sender']) ? $params['sender'] : $this->sender,
            'DestinationAddress'    => $params['to'],
            'Data'                  => $params['text'],
        ];

        try {
            $response = $this->client->request('POST', $this->url, ['json' => $post, 'headers' => $headers]);
            $body = \json_decode((string) $response->getBody(), true);

            if ($response->getStatusCode() !== 200) {
                throw ErrorSendNotification::responseError($body);
            }

            DB::table('sms_devino')->insert(
                array(
                    'from'          =>   !empty($params['sender']) ? $params['sender'] : $this->sender,
                    'to'            =>   $params['to'],
                    'message'       =>   addslashes($params['text']),
                    'status'        =>   'new',
                    'extra'         =>   json_encode(['devino_id'=>$body[0]]),
                    'created_at'    =>   date('Y-m-d H:i:s')
                )
            );


            return $body;
        } catch (\Exception $exception) {
            throw ErrorSendNotification::connectError($exception);
        }
    }
}
