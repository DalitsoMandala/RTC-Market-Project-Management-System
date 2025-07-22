<?php

namespace App\Livewire\Internal\Cip\Markets;

use Carbon\Carbon;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Traits\CheckProgressTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Imports\MarketImport\MarketImportSheet;
use App\Exports\MarketingExport\MarketingDataExport;
use App\Imports\AttendanceImport\AttendanceRegistersMultiSheetImport;

class SubmitData extends Component
{
    use WithFileUploads;
    use LivewireAlert;

    #[Validate('required')]
    public $upload;
    public $uploadMultiple = [];
    public $importId;
    public $currentRoute;

    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;


    public function checkProgress()
    {
        $jobProgress = JobProgress::where('cache_key', $this->importId)->first();

        $this->progress = $jobProgress ? $jobProgress->progress : 0;
        $this->importing = true;
        $this->importingFinished = false;

        if ($jobProgress && $jobProgress->status == 'failed') {
            Cache::forget($this->importId);
            session()->flash('error', 'An error occurred during the import! --- ' . $jobProgress->error);

            $this->redirect(url()->previous());
        } else if ($jobProgress && $jobProgress->status == 'completed') {


            $user = User::find(auth()->user()->id);
            Cache::forget($this->importId);

            if ($user->hasAnyRole('external')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('external-submissions') . '#market-submission');
            } else if ($user->hasAnyRole('staff')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('cip-staff-submissions') . '#market-submission');
            } else if ($user->hasAnyRole('admin')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('admin-submissions') . '#market-submission');
            } else if ($user->hasAnyRole('enumerator')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('enumerator-submissions') . '#market-submission');
            } else {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('cip-submissions') . '#market-submission');
            }
        }
    }

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
            //code...

            $userId = auth()->user()->id;
            $user = User::find($userId);


            if ($this->upload) {
                $name = 'market' . time() . '.' . $this->upload->getClientOriginalExtension();
                $directory = 'public/imports';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                $this->upload->storeAs($directory, $name);
                $path = storage_path('app/public/imports/' . $name);
                try {


                    Excel::import(new MarketImportSheet(cacheKey: $this->importId, filePath: $path, submissionDetails: [
                        'user_id' => Auth::user()->id,
                        'organisation_id' => Auth::user()->organisation->id,
                        'file_link' => $name,
                        'batch_no' => $this->importId,
                        'route' => $this->currentRoute,
                    ]), $path);
                    $this->checkProgress();
                } catch (ExcelValidationException $th) {


                    session()->flash('error', $th->getMessage());
                    Log::error($th);
                    $this->redirect(url()->previous());
                }
            }
        } catch (\Exception $th) {
            //throw $th;

            session()->flash('error', 'Something went wrong!');
            Log::error($th);
            $this->redirect(url()->previous());
        }

        $this->removeTemporaryFile();
    }

    public function save() {}

    public function mount()
    {
        $this->importId = Uuid::uuid4()->toString();
        $this->currentRoute = url()->current();
    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new MarketingDataExport(true), 'marketing_report_template.xlsx');
    }

    public function removeTemporaryFile()
    {
        // Get the temporary file path
        if ($this->upload) {
            $temporaryFilePath = $this->upload->getRealPath();

            // Check if the file exists and delete it
            if (file_exists($temporaryFilePath)) {
                try {
                    unlink($temporaryFilePath);
                } catch (\Exception $e) {
                    // Handle the exception (e.g., log the error)
                    Log::error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }
        }
    }
    public function render()
    {
        return view('livewire.internal.cip.markets.submit-data');
    }
}
