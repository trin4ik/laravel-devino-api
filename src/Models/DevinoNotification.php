<?php

namespace Trin4ik\DevinoApi\Models;

use Illuminate\Database\Eloquent\Model;

class DevinoNotification extends Model
{
	protected $guarded = [];

	protected $casts = [
		'log' => 'json',
	];

	public function notifable (): MorphTo {
		return $this->morphTo();
	}


}