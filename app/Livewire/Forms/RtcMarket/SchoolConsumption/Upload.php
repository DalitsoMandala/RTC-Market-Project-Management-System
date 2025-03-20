<?php

namespace App\Livewire\Forms\RtcMarket\SchoolConsumption;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
use App\Traits\UploadDataTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use App\Traits\CheckProgressTrait;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Exports\SchoolExport\SchoolRtcConsumptionExport;
use App\Imports\SchoolImport\SchoolRtcConsumptionImport;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImport;
use App\Imports\SchoolImport\SchoolRtcConsumptionMultiSheetImport;
use App\Exports\ExportProcessor\RtcProductionProcessorsMultiSheetExport;
use App\Imports\ImportProcessor\RtcProductionProcessorsMultiSheetImport;

class Upload extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    use CheckProgressTrait;
    use UploadDataTrait;


    public function submitUpload()
    {
        try {
            $this->validate();
        } catch (\Throwable $e) {
            $this->dispatch('errorRemove');
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        try {
            if ($this->upload) {


                // Use the trait method to upload the file
                $fileName = $this->uploadFile(
                    file: $this->upload,
                    importId: $this->importId,
                    importClass: SchoolRtcConsumptionMultiSheetImport::class // Pass the import class
                );

                $submissionDetails['file_link'] = $fileName;

                $this->checkProgress();
            }
        } catch (ValidationException $e) {
            session()->flash('error', $e->getMessage());
            $this->redirect(url()->previous());
        } catch (\Exception $e) {
            session()->flash('error', 'Something went wrong!');
            Log::error($e);
            $this->redirect(url()->previous());
        } finally {
            if (isset($fileName)) {
                // Use the trait method to remove the temporary file
                $this->removeTemporaryFile($fileName);
            }
        }
    }


    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new SchoolRtcConsumptionExport(true), 'school_consumption_template.xlsx');
    }




    public function render()
    {
        return view('livewire.forms.rtc-market.school-consumption.upload');
    }
}
