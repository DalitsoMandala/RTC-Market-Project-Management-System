<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;


use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Imports\rtcmarket\HouseholdImport\HrcImport;
use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ViewData extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $epa;

    public $section;

    public $district;

    public $enterprise;

    #[Validate('required')]
    public $upload;

    public $period;

    public function mount()
    {
        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM FARMERS')->first();
        $period = $form->submissionPeriods->where('is_open', true)->first();
        if ($period) {
            $this->period = $period->id;
        } else {
            $this->period = null;
        }
    }

    public function submitUpload()
    {

        $this->validate();
        try {
            //code...

            $userId = auth()->user()->id;

            if ($this->upload) {
                $form = Form::where('name', 'HOUSEHOLD CONSUMPTION FORM')->first();

                $submissionCount = Submission::where('period_id', $this->period)->where('form_id', $form->id)->whereNot('status', 'approved')->count();

                if ($submissionCount > 0) {
                    throw new \Exception("You can not submit your data right now! Wait for your approval");

                }

                $import = Excel::import(new HrcImport($userId), $this->upload);

                $uuid = session()->get('uuid');
                $batch_data = session()->get('batch_data');
                $name = 'hrc_' . time() . $uuid . '.' . $this->upload->getClientOriginalExtension();
                $this->upload->storeAs(path: 'imports', name: $name);
                $currentUser = Auth::user();

                if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {
                    $submission = Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $form->id,
                        'user_id' => $currentUser->id,
                        'status' => 'approved',
                        'data' => json_encode($batch_data),
                        'batch_type' => 'batch',
                    ]);

                    $data = json_decode($submission->data, true);

                    foreach ($data as $row) {

                        $main_food_data = $row['main_food_data'];
                        $insert = HouseholdRtcConsumption::create($row);

                    }

                } else if ($currentUser->hasAnyRole('external')) {

                    Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $form->id,
                        'period_id' => $this->period,
                        'user_id' => $currentUser->id,
                        'data' => json_encode($batch_data),
                        'batch_type' => 'batch',
                    ]);

                }

                $this->reset();
                $this->dispatch('removeUploadedFile');
                $this->dispatch('refresh');
                session()->flash('success', 'Successfully uploaded your data!');
            }

        } catch (\Throwable $th) {
            //throw $th;

            session()->flash('error', $th->getMessage());
            // Log::error($th);

        }

        $this->removeTemporaryFile();

    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new HrcExport, 'household_rtc_consumption_template_' . $time . '.xlsx');

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
                    \Log::error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }

        }

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.view-data');
    }
}
