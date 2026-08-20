<?php
namespace App\Livewire\Forms\RtcMarket\ProductionMarketingLog;

use App\Exceptions\ExcelValidationException;
use App\Exports\MarketingLog\MarketingLogMultiSheet;
use App\Imports\MarketingLog\MarketingLogImportMultiSheet;
use App\Models\Form;
use App\Traits\CheckProgressTrait;
use App\Traits\UploadDataTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class Upload extends Component
{
    use WithFileUploads;
    use LivewireAlert;
    use CheckProgressTrait;
    use UploadDataTrait;
    public string $form_name = '';
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
                    importClass: MarketingLogImportMultiSheet::class,
                    name_prefix: 'production_marketing_log',
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
        }
    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new MarketingLogMultiSheet(true), 'production_marketing_log_' . $time . '.xlsx');
    }

    public function save()
    {

    }

    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.production-marketing-log.upload');
    }
}
