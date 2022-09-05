<?php

namespace Trin4ik\DevinoApi\Exceptions;

use Exception;

class DevinoIncorrectMessageException extends Exception
{
	public function __construct () {
		parent::__construct('incorrect [DevinoMessage] class');
	}
}