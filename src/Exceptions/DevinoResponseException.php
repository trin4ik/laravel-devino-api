<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class DevinoResponseException extends Exception
{
	public function __construct (\Exception|array $e) {
		$message = is_array($e) ? json_encode($e) : $e->getMessage();
		parent::__construct('wrong response from devino: ' . $message);
	}
}