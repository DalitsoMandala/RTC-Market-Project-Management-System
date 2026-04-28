<?php

namespace App\Http\Controllers;

use App\Traits\IndicatorsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;

class ReminderController extends Controller
{
    use IndicatorsTrait;
     public function send()
    {

        Bus::dispatch(new \App\Jobs\InstantSendReminderJob());
        return response()->json(['message' => 'Submission reminders sent successfully.']);
    }
    //


}
