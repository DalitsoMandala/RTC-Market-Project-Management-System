<?php

namespace App\Livewire\Forms\RtcMarket\RtcRecruitment;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\ImportError;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
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
use App\Exceptions\UserErrorException;
use App\Notifications\JobNotification;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Exports\RtcRecruitment\RtcRecruitmentExport;
use App\Imports\RtcRecruitment\RtcRecruitmentMultiSheet;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImport;
use App\Exports\ExportFarmer\RtcProductionFarmersMultiSheetExport;
use App\Imports\ImportFarmer\RtcProductionFarmersMultiSheetImport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\RtcRecruitment\RtcRecruitmentMultiSheetExport;
use App\Traits\UploadDataTrait;

class Upload extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    use CheckProgressTrait;
    use UploadDataTrait;
    public $form_name;
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
                    importClass: RtcRecruitmentMultiSheet::class,
                    name_prefix: 'rtc_recruitment',
                );

                $submissionDetails['file_link'] = $fileName;

                $this->checkProgress();
            }
        } catch (ExcelValidationException $th) {

            session()->flash('error', $th->getMessage());
            Log::error($th);
            $this->redirect(url()->previous());
        } catch (\Exception $e) {
            Log::error($e);
            session()->flash('error', 'Something went wrong!');
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

        return Excel::download(new RtcRecruitmentMultiSheetExport(true), 'rtc_actor_recruitment_template.xlsx');
    }


    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.rtc-recruitment.upload');
    }
}
