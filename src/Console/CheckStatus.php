<?php

namespace Trin4ik\DevinoApi\Console;

use Illuminate\Console\Command;
use Trin4ik\DevinoApi\Facades\DevinoApi AS SMS;
use Illuminate\Support\Facades\DB;
use Trin4ik\DevinoApi\Models\DevinoItem;

class CheckStatus extends Command
{
    protected $signature = 'devino:check';

    protected $description = 'Check sms status';

    public function handle()
    {
        $check_status = ['new', 'scheduled', 'enroute', 'sent'];
        $this->info('Check sms status middle status...');

        $count = DevinoItem::wherein('status', $check_status)->count();
        $this->info('Total checks: '.$count.':');

        $sms = DevinoItem::wherein('status', $check_status)->get();
        foreach ($sms AS $item) {
            $old_status = $item->status;
            $result = SMS::check($item);
            if ($result['success']) {
                $this->info('check id ' . $item->id .', status "' . $old_status . '" -> "' . $result['data'] . '"');
            } else {
                $this->info('check id ' . $item->id .' error: "' . $result['data'] . '"');
            }
        }

        $this->info('Check sms done!');
    }
}