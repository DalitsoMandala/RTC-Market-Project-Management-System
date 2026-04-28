<?php

namespace App\Http\Controllers;

use App\Traits\IndicatorsTrait;
use Illuminate\Http\Request;


class ReminderController extends Controller
{
    use IndicatorsTrait;
     public function send()
    {
        $this->getEndingSoonSubmissionPeriods();
        return response()->json(['message' => 'Submission reminders sent successfully.']);
    }
    //


}
