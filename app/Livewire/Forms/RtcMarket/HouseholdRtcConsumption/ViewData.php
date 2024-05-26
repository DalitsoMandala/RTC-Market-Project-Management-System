<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Exports\rtcmarket\HrcExport;
use App\Imports\rtcmarket\HrcImport;
use App\Livewire\HouseholdRtcConsumptionTable;
use App\Models\HrcLocation;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ViewData extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    #[Validate('required')]
    public $epa;
    #[Validate('required')]
    public $section;
    #[Validate('required')]
    public $district;

    #[Validate('required')]
    public $enterprise;

    #[Validate('required')]
    public $upload;

    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function submitUpload()
    {

        $this->validate();
        try {
            //code...

            $userId = auth()->user()->id;
            $location = HrcLocation::create([
                'enterprise' => $this->enterprise,
                'district' => $this->district,
                'epa' => $this->epa,
                'section' => $this->section,
            ]);

            if ($this->upload) {

                $import = Excel::import(new HrcImport($location->id, $userId), $this->upload);

                $this->reset();
                $this->dispatch('removeUploadedFile');
                $this->dispatch('refresh')->to(HouseholdRtcConsumptionTable::class);
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
