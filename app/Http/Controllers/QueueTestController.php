<?php
namespace App\Http\Controllers;

use App\Traits\ReportsTrait;

class QueueTestController extends Controller
{
    use ReportsTrait;

    public function handle()
    {
        $this->run();
    }

    public function initZeroReport()
    {
        $this->initIgnoredYears();
    }
}
