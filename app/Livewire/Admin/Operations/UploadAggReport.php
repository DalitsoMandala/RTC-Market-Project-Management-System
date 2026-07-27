<?php
namespace App\Livewire\Admin\Operations;

use App\Exceptions\ExcelValidationException;
use App\Exports\AggExport\AggExportSheet;
use App\Helpers\CoreFunctions;
use App\Imports\AggImport\AggImportMultiSheet;
use App\Models\JobProgress;
use App\Models\Organisation;
use App\Models\User;
use App\Traits\CheckProgressTrait;
use App\Traits\UploadDataTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;

class UploadAggReport extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    use CheckProgressTrait;
    use UploadDataTrait;
    #[Validate('required')]
    public $upload;
    public $variable;
    public $rowId;
    public $selectedIndicator;
    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;

    public $selectedForm;

    public $submissionPeriodId;

    public $openSubmission = false;
    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId;

    public $queue = false;
    public $targetSet = false;
    public $targetIds = [];
    public $currentRoute;
    public $selectedCrop = 'All Crops';
    public $selectedOrganisation = null;
    public $form_name;

    public function save()
    {

    }

    protected function rules()
    {

        return [
            'upload'               => 'required|mimes:xlsx',
            'selectedCrop'         => 'required',
            'selectedOrganisation' => 'required',
        ];
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
            $user   = User::find($userId);

            if ($this->selectedCrop === 'All Crops') {
                $this->selectedCrop = null;
            }

            if ($this->upload) {
                $name      = 'agg' . time() . '.' . $this->upload->getClientOriginalExtension();
                $directory = 'public/imports';
                if (! Storage::exists($directory)) {
                    Storage::makeDirectory($directory);
                }

                $this->upload->storeAs($directory, $name);
                $path = storage_path('app/public/imports/' . $name);

                try {

                    Excel::import(new AggImportMultiSheet(
                        user_id: $userId,
                        organisation_id: $this->selectedOrganisation,
                        uuid: $this->importId,
                        filePath: $path,
                        submissionDetails: [
                            'batch_no' => $this->importId,
                            'route'    => $this->currentRoute,
                            'crop'     => $this->selectedCrop,

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

    }

    public function checkProgress()
    {
        $jobProgress = JobProgress::where('cache_key', $this->importId)->first();

        $this->progress          = $jobProgress ? $jobProgress->progress : 0;
        $this->importing         = true;
        $this->importingFinished = false;

        if ($jobProgress && $jobProgress->status == 'failed') {
            Cache::forget($this->importId);
            session()->flash('error', 'An error occurred during the import! --- ' . $jobProgress->error);

            $this->redirect(url()->previous());
        } else if ($jobProgress && $jobProgress->status == 'completed') {

            $user = User::find(auth()->user()->id);
            Cache::forget($this->importId);
            if ($user->hasAnyRole('admin')) {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('admin-aggregated-reports'));
            } else {
                session()->flash('success', 'Successfully submitted!');
                $this->redirect(route('cip-aggregated-reports'));
            }
        }
    }
    public function send()
    {
        $user = User::find(auth()->user()->id);

        $this->redirect(url()->previous());
    }

    public function mount()
    {

        $this->importId = Uuid::uuid4()->toString();

        $this->currentRoute = url()->current();
    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new AggExportSheet(true), 'agg_template.xlsx');
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
        return view('livewire.admin.operations.upload-agg-report', [
            'organisations' => Organisation::all(),
            'crops'         => CoreFunctions::getCropsWithNull(),
        ]);
    }
}
