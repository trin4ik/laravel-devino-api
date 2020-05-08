<?php

namespace Trin4ik\DevinoApi;

use GuzzleHttp\Client as HttpClient;
use Trin4ik\DevinoApi\Exceptions\ErrorSendNotification;

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
            'Validity'              => !empty($params['validity']) ? $params['validity'] : $this->sender,
        ];

        try {
            $response = $this->client->request('POST', $this->url, ['body' => $post, 'headers' => $headers]);
            $body = $response->getBody();
            $response = \json_decode((string) $response->getBody(), true);

            if ($response['result'][0]['code'] !== 'OK') {
                throw ErrorSendNotification::responseError($body);
            }
            return $response;
        } catch (\Exception $exception) {
            throw ErrorSendNotification::connectError($exception);
        }
    }
}
