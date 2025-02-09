<?php

namespace App\Livewire\Imports;

use Exception;
use Throwable;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgresSummaryExport;
use App\Imports\ProgresSummaryImport;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;

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
    public $showInput = true;

    public function downloadTemplate()
    {
        $time = \Carbon\Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new ProgresSummaryExport(true), 'progress_summary_report_template.xlsx');
    }
    public function submitUpload()
    {
        $this->showInput = false;
        try {
            $this->validate([
                'upload' => 'required|file|mimes:xlsx,csv',

                'selectedReportingPeriod' => 'required',
                'selectedOrganisation' => 'required',
            ], attributes: [
                'selectedFinancialYear' => 'Project Year',
            ]);
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            $this->showInput = true;
            throw $e;
        }


        try {

            $this->showInput = false;
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

                    user_id: Auth::user()->id,
                    organisation_id: $this->selectedOrganisation,
                    uuid: $this->importId,
                    file_link: $name
                ), $path);
                // Clear the file input after upload
                session()->flash('success', 'File uploaded successfully. Report will be updated very soon!');
                $this->redirect(url()->previous());
            } catch (Exception $th) {

                $this->showInput = true;
                session()->flash('error', $th->getMessage());
                Log::error($th);
            }
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            $this->showInput = true;
            throw $e;
        }

        $this->dispatch('removeUploadedFile');
        $this->showInput = true;
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
