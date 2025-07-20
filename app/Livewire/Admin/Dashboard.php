<?php

namespace App\Livewire\Admin;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\SystemReport;
use App\Models\FinancialYear;

use App\Models\SystemReportData;
use Livewire\Attributes\Validate;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Lazy;


class Dashboard extends Component
{



    public $showContent = false;

    #[On('showCharts2')]
    public function showVisuals()
    {

        $this->showContent = true;
    }




    public function render()
    {

        return view('livewire.admin.dashboard');
    }
}
