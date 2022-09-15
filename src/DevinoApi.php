<?php

namespace Trin4ik\DevinoApi;

use GuzzleHttp\Client;
use Trin4ik\DevinoApi\Enums\DevinoNotificationStatus;
use Trin4ik\DevinoApi\Exceptions\DevinoResponseException;
use Trin4ik\DevinoApi\Exceptions\DevinoConnectingException;
use Trin4ik\DevinoApi\Exceptions\DevinoSendException;
use Trin4ik\DevinoApi\Exceptions\DevinoUnknownStatusException;
use Trin4ik\DevinoApi\Exceptions\DevinoWrongParamsException;
use Trin4ik\DevinoApi\Exceptions\ErrorSendNotification;
use Illuminate\Support\Facades\DB;
use Trin4ik\DevinoApi\Models\DevinoNotification;

class DevinoApi
{
	protected $client;
	protected $url;
	protected $login;
	protected $password;
	protected $sender;
	protected $validity;

	public function __construct () {
		$this->login = config('devino.auth_plain.login');
		$this->password = config('devino.auth_plain.password');
		$this->sender = config('devino.sender');
		$this->url = config('devino.url');
		$this->validity = config('devino.validity');

		$this->client = new Client([
			'timeout'         => 10,
			'connect_timeout' => 10,
		]);
	}

	protected function headers (): array {
		return [
			'Content-Type' => 'application/json',
			'Accept'       => 'application/json'
		];
	}

	public function send (array $params): array {
		$params['sender'] = $params['sender'] ?? $this->sender;

		if (empty($params['sender']) || empty($params['to']) || empty($params['message'])) {
			throw new DevinoWrongParamsException($params);
		}

		$json = [
			'Login'              => $this->login,
			'Password'           => $this->password,
			'SourceAddress'      => $params['sender'],
			'DestinationAddress' => $params['to'],
			'Data'               => $params['message'],
		];

		try {
			$response = $this->query(
				method: 'POST',
				url: '/Send',
				request: [
					'json'    => $json,
					'headers' => $this->headers()
				]
			);

			if (!isset($response[0])) {
				throw new DevinoResponseException($response);
			}

			return $response;
		} catch (\Exception $e) {
			throw new $e;
		}
	}

	public function check (DevinoNotification|string $sms): DevinoNotificationStatus {
		$query = [
			'Login'     => $this->login,
			'Password'  => $this->password,
			'messageId' => is_string($sms) ? $sms : $sms->devino_id
		];

		try {
			$response = $this->query(
				url: '/State',
				request: [
					'query'   => $query,
					'headers' => $this->headers()
				]
			);

			if (!isset($response['State'])) {
				throw new DevinoResponseException($response);
			}

			return DevinoNotificationStatus::fromResponse($response['State']);
		} catch (\Exception $e) {
			throw new $e;
		}
	}

	public function log (DevinoNotification $sms, mixed $log, string $type = 'status'): void {
		$sms->log = [...$sms->log, [
			'date' => time(),
			'type' => $type,
			'data' => $log
		]];
		$sms->save();
	}

	protected function query (
		string $method = 'GET',
		string $url = '',
		array  $request = []
	): array {
		try {
			$response = $this->client->request($method, $this->url . $url, $request);
			return json_decode((string)$response->getBody(), true);
		} catch (\DomainException $e) {
			throw new DevinoConnectingException($e);
		} catch (\Exception $e) {
			throw new DevinoResponseException($e);
		}
	}
}
