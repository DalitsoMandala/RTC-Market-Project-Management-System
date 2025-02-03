<?php

namespace App\Livewire\Imports;

use Throwable;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProgresSummaryImport;
use App\Exceptions\ExcelValidationException;
use Exception;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;
use Ramsey\Uuid\Uuid;

class ImportData extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId;
    public $upload;
    public $selectedFinancialYear;
    public $selectedReportingPeriod;
    public $selectedOrganisation;

    public $organisations;
    public $financialYears;
    public $reportingPeriod;


    public function submitUpload()
    {

        try {

            $this->validate([
                'upload' => 'required|file|mimes:xlsx,csv',
                'selectedFinancialYear' => 'required',
                'selectedReportingPeriod' => 'required',
                'selectedOrganisation' => 'required',
            ], attributes: [
                'selectedFinancialYear' => 'Project Year',
            ]);
            $name = 'seed' . time() . '.' . $this->upload->getClientOriginalExtension();
            $this->upload->storeAs('public/imports', $name);

            // Use storage_path to get the absolute path
            $path = storage_path('app/public/imports/' . $name);

            $this->importing = true;
            $this->importingFinished = false;
            try {

                $this->importId = Uuid::uuid4()->toString();
                Excel::import(new ProgresSummaryImport(
                    filePath: $path,
                    financial_year_id: $this->selectedFinancialYear,
                    user_id: Auth::user()->id,
                    organisation_id: Auth::user()->organisation->id,
                    uuid: $this->importId,
                    file_link: $name
                ), $path);
                $this->reset('upload'); // Clear the file input after upload
            } catch (Exception $th) {


                session()->flash('error', $th->getMessage());
                Log::error($th);
            }
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            dd($e);
            throw $e;
        }



        // Use Excel import (Assumes you have set up an Import for SeedBeneficiaries)




    }
    public function save() {}

    public function mount()
    {

        $this->financialYears = FinancialYear::get();
        $this->reportingPeriod = ReportingPeriodMonth::get();
        $this->organisations = Organisation::get();
        $this->selectedReportingPeriod = ReportingPeriodMonth::where('type', 'UNSPECIFIED')->first()->id;
    }


    public function render()
    {
        return view('livewire.imports.import-data');
    }
}
