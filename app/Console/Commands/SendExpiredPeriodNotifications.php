<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Indicator;
use App\Models\MailingList;
use App\Models\Organisation;
use App\Traits\IndicatorsTrait;
use Illuminate\Console\Command;
use App\Models\SubmissionPeriod;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendExpiredPeriodNotificationJob;
use App\Traits\GroupsEndingSoonSubmissionPeriods;

class SendExpiredPeriodNotifications extends Command
{
    use IndicatorsTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:expired-period-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for expired submission periods';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $this->notifyExpiredSubmissionPeriods();
    }
}
