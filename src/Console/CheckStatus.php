<?php

namespace Trin4ik\DevinoApi\Console;

use Illuminate\Console\Command;
use Trin4ik\DevinoApi\Facades\DevinoApi as SMS;
use Illuminate\Support\Facades\DB;
use Trin4ik\DevinoApi\Models\DevinoNotification;

class CheckStatus extends Command
{
	protected string $signature = 'devino:check';
	protected string $description = 'Check sms status';

	public function handle () {
		$check_status = ['new', 'scheduled', 'enroute', 'sent'];
		$this->line('Check sms status middle status...');

		$query = DevinoNotification::whereIn('status', $check_status);
		$this->line('Total checks: ' . $query->count());

		$items = $query->get();
		foreach ($items as $item) {
			try {
				$status = SMS::check($item);
				SMS::log($item, $status);
				$this->info('check id ' . $item->id . ', status "' . $item->status . '" -> "' . $status . '"');
			} catch (\Exception $e) {
				$this->error('check id ' . $item->id . ' error: "' . $e->getMessage() . '"');
			}
		}

		$this->line('Check sms done!');
	}
}