<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class DevinoUnknownStatusException extends Exception
{
	public function __construct (array $json) {
		parent::__construct('unknown status from devino: ' . json_encode($json));
	}
}