<?php

namespace App\Livewire\Internal\Cip;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SystemReportData;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;


class Dashboard extends Component
{




    public function render()
    {
        return view('livewire.internal.cip.dashboard');
    }
}
