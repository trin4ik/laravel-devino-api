<?php

namespace Trin4ik\DevinoApi\Enums;

enum DevinoNotificationStatus: string
{
	case Sent = 'sent';
	case Scheduled = 'scheduled';
	case Deleted = 'deleted';
	case Delivered = 'delivered';
	case Undeliverable = 'undeliverable';
	case Rejected = 'rejected';
	case Expired = 'expired';
	case Unknown = 'unknown';
	case New = 'new';

	public static function values (): array {
		return array_column(self::cases(), 'value');
	}

	public static function fromResponse (int $status = 99): DevinoNotificationStatus {
		return match ($status) {
			-1         => DevinoNotificationStatus::Sent,
			-2         => DevinoNotificationStatus::Scheduled,
			-98, 47    => DevinoNotificationStatus::Deleted,
			0          => DevinoNotificationStatus::Delivered,
			10, 11, 41 => DevinoNotificationStatus::Undeliverable,
			42, 48, 69 => DevinoNotificationStatus::Rejected,
			46         => DevinoNotificationStatus::Expired,
			255        => DevinoNotificationStatus::New,
			default    => DevinoNotificationStatus::Unknown
		};
	}
}