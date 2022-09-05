<?php

namespace Trin4ik\DevinoApi\Facades;

use Illuminate\Support\Facades\Facade;

class Devino extends Facade
{
	protected static function getFacadeAccessor () {
		return 'devino';
	}
}