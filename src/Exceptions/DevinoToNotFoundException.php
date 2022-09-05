<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class DevinoToNotFoundException extends Exception
{
	public function __construct (string $model) {
		parent::__construct('no [toDevino] method in model: ' . $model);
	}
}