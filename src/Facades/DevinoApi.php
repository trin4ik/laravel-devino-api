<?php

namespace Trin4ik\DevinoApi\Facades;

use Illuminate\Support\Facades\Facade;

class DevinoApi extends Facade
{
	protected static function getFacadeAccessor () {
		return 'sms';
	}
}