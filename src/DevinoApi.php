<?php

namespace Trin4ik\DevinoApi;

use GuzzleHttp\Client as HttpClient;
use Trin4ik\DevinoApi\Exceptions\ErrorSendNotification;
use Illuminate\Support\Facades\DB;
use Trin4ik\DevinoApi\Models\DevinoItem;

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
        $param = [
            'Login'                 => $this->login,
            'Password'              => $this->password,
            'SourceAddress'         => !empty($params['sender']) ? $params['sender'] : $this->sender,
            'DestinationAddress'    => $params['to'],
            'Data'                  => $params['text'],
        ];

        try {
            $response = $this->client->request('POST', $this->url . '/Send', ['json' => $param, 'headers' => $headers]);
            $body = \json_decode((string) $response->getBody(), true);

            $sms = new DevinoItem([
                'devino_id'     =>   $body[0],
                'from'          =>   !empty($params['sender']) ? $params['sender'] : $this->sender,
                'to'            =>   $params['to'],
                'message'       =>   addslashes($params['text'])
            ]);
            $sms->save();

        } catch (\DomainException $e) {
            throw ErrorSendNotification::connectError($e);
        } catch (\Exception $e) {
            throw ErrorSendNotification::responseSendError($e);
        }
    }

    public function check (DevinoItem $sms) {
        $headers = [
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
        $param = [
            'Login'                 => $this->login,
            'Password'              => $this->password,
            'messageId'             => $sms->devino_id
        ];

        try {
            $response = $this->client->request('GET', $this->url . '/State', ['query' => $param, 'headers' => $headers]);
            $body = json_decode($response->getBody(), true);

            $status = false;
            switch ($body['State']) {
                case -1: { // sent
                    $status = 'sent';
                    break;
                }
                case -2: { // scheduled
                    $status = 'scheduled';
                    break;
                }
                case -98:
                case 47: { // deleted
                    $status = 'deleted';
                    break;
                }

                case 0: { // delivered
                    $status = 'delivered';
                    break;
                }

                case 11:
                case 41:
                case 10: { // undeliverable
                    $status = 'undeliverable';
                    break;
                }
                case 48:
                case 69:
                case 42: { // rejected
                    $status = 'rejected';
                    break;
                }
                case 46: { // expired
                    $status = 'expired';
                    break;
                }
                case 99: { // unknown
                    $status = 'unknown';
                    break;
                }
                case 255: { // new
                    $status = 'new';
                    break;
                }
            }

            if ($status) {
                if ($sms->status !== $status) {
                    $sms->status = $status;
                    $tmp = $sms->log;
                    $tmp[] = ['date' => time(), 'type'=> 'status', 'data' => (string) $response->getBody()];
                    $sms->log = $tmp;
                    $sms->save();
                    return ['success' => true, 'data' => $status];
                } else {
                    return ['success' => true, 'data' => 'same status'];
                }
            } else {
                throw new \Exception("unknown status: " . $response->getBody());
            }
        } catch (\DomainException $e) {
            return ['success' => false, 'data' => 'domain connect'];
        } catch (\Exception $e) {
            return ['success' => false, 'data' => $e->getMessage() . ': '. $e->getLine()];
        }

    }
}
