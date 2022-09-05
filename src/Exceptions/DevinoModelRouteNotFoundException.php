<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class DevinoModelRouteNotFoundException extends Exception
{
	public function __construct (string $model) {
		parent::__construct('no [routeNotificationForDevino] method in model: ' . $model);
	}
}