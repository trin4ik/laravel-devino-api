<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class DevinoConnectingException extends Exception
{
	public function __construct (\Exception $e) {
		parent::__construct('cant connect to devino: ' . $e->getMessage());
	}
}