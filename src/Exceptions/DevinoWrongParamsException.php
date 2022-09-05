<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class DevinoWrongParamsException extends Exception
{
	public function __construct (array $params) {
		parent::__construct('wrong params, when sending to devino: ' . json_encode($params));
	}
}