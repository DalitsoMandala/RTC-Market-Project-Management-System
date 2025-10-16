<?php

namespace App\Livewire\Internal\Manager;

use App\Exports\AttendanceExport\AttendanceRegistersExport;
use App\Exports\ExportFarmer\RtcProductionFarmersMultiSheetExport;
use App\Exports\ExportProcessor\RtcProductionProcessorsMultiSheetExport;
use App\Exports\RtcConsumption\RtcConsumptionExport;
use App\Exports\SeedBeneficiariesExport;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class Forms extends Component
{
        use LivewireAlert;


  

    public function mount(){

    }


    public function render()
    {
        return view('livewire.internal.manager.forms');
    }
}
