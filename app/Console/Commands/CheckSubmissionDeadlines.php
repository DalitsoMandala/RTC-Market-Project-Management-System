<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use Illuminate\Console\Command;
use App\Models\SubmissionPeriod;
use App\Jobs\sendReminderToUserJob;
use Illuminate\Support\Facades\Bus;
use App\Notifications\SubmissionReminder;
use App\Traits\GroupsEndingSoonSubmissionPeriods;
use App\Traits\IndicatorsTrait;

class CheckSubmissionDeadlines extends Command
{
    protected $signature = 'check:submission-deadlines';
    protected $description = 'Check submission deadlines and send reminders to users';

use IndicatorsTrait;
    public function handle()
    {

     $this->getEndingSoonSubmissionPeriods();
        $this->info('Submission reminders sent successfully.');
    }
}
