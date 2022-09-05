<?php

namespace Trin4ik\DevinoApi\Notifications;

use Illuminate\Notifications\Notification;
use Trin4ik\DevinoApi\Exceptions\DevinoModelRouteNotFoundException;
use Trin4ik\DevinoApi\Exceptions\DevinoToNotFoundException;
use Trin4ik\DevinoApi\Facades\DevinoApi as SMS;
use Trin4ik\DevinoApi\Models\DevinoNotification;

class DevinoChannel
{
	public function send ($notifiable, Notification $notification): bool {
		if (!method_exists($notifiable, 'routeNotificationForDevino')) {
			throw new DevinoModelRouteNotFoundException(get_class($notifiable) . ':' . $notifiable->getKey());
		}
		if (!method_exists($notification, 'toDevino')) {
			throw new DevinoToNotFoundException(get_class($notification));
		}

		$message = [
			'to'      => $notifiable->routeNotificationForDevino($notifiable),
			'message' => $notification->toDevino($notifiable),
			'sender'  => method_exists($notification, 'sender') ? $notification->sender($notifiable) : config('devino.sender'),
		];

		$sms = SMS::send($message);

		DevinoNotification::create([
			'notifable_type' => get_class($notifiable),
			'notifable_id'   => $notifiable->getKey(),
			'devino_id'      => $sms[0],
			...$message,
		]);

		return true;
	}
}
