<?php

namespace App\Livewire\OtherForms\SeedBeneficiaries;

use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use App\Models\SeedBeneficiary;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Exports\SeedBeneficiariesExport;
use App\Imports\SeedBeneficiariesImport;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Add extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    public $district;
    public $epa;
    public $section;
    public $name_of_aedo;
    public $aedo_phone_number;
    public $date;
    public $name_of_recipient;
    public $village;
    public $sex;
    public $age;
    public $marital_status;
    public $hh_head;
    public $household_size;
    public $children_under_5;
    public $variety_received;
    public $bundles_received;
    public $phone_or_national_id;
    public $crop = 'OFSP';
    public $upload;

    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId;

    public $routePrefix;
    protected $rules = [
        'district' => 'required|string|max:255',
        'epa' => 'required|string|max:255',
        'section' => 'required|string|max:255',
        'name_of_aedo' => 'required|string|max:255',
        'aedo_phone_number' => 'required|string|max:20',
        'date' => 'required|date',
        'name_of_recipient' => 'required|string|max:255',
        'village' => 'required|string|max:255',
        'sex' => 'required|integer|in:1,2',
        'age' => 'required|integer|min:1',
        'marital_status' => 'required|integer|in:1,2,3,4',
        'hh_head' => 'required|integer|in:1,2,3',
        'household_size' => 'required|integer|min:1',
        'children_under_5' => 'required|integer|min:0',
        'variety_received' => 'required|string|max:255',
        'bundles_received' => 'required|integer|min:1',
        'phone_or_national_id' => 'required|string|max:20',
        'crop' => 'required|string|in:OFSP,Potato,Cassava',
    ];

    public function checkProgress()
    {
        $jobProgress = JobProgress::where('cache_key', $this->importId)->first();

        $this->progress = $jobProgress ? $jobProgress->progress : 0;
        $this->importing = true;
        $this->importingFinished = false;


        if ($this->progress == 100) {
            $this->importing = false;
            $this->importingFinished = true;


            $this->importId = Uuid::uuid4()->toString(); // change key

            if ($jobProgress->status == 'failed') {

                session()->flash('error', 'An error occurred during the import! --- ' . $jobProgress->error);
                $this->reset('upload');
            } else if ($jobProgress->status == 'completed') {
                $this->reset('upload');

                $this->dispatch('complete-submission');
            }
            $this->dispatch('removeUploadedFile');

        }
    }

    public function send()
    {

        session()->flash('success', 'File uploaded successfully! <a href="' . $this->routePrefix . '/seed-beneficiaries">View Submission here</a>');
    }


    public function save()
    {

        try {

            $this->validate();



        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }


        try {

            SeedBeneficiary::create([
                'district' => $this->district,
                'epa' => $this->epa,
                'section' => $this->section,
                'name_of_aedo' => $this->name_of_aedo,
                'aedo_phone_number' => $this->aedo_phone_number,
                'date' => $this->date,
                'name_of_recipient' => $this->name_of_recipient,
                'village' => $this->village,
                'sex' => $this->sex,
                'age' => $this->age,
                'marital_status' => $this->marital_status,
                'hh_head' => $this->hh_head,
                'household_size' => $this->household_size,
                'children_under_5' => $this->children_under_5,
                'variety_received' => $this->variety_received,
                'bundles_received' => $this->bundles_received,
                'phone_or_national_id' => $this->phone_or_national_id,
                'crop' => $this->crop,
                'user_id' => auth()->user()->id,
            ]);

            session()->flash('success', 'Seed Beneficiary added successfully.');

        } catch (\Throwable $th) {


            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());
        }


    }

    public function uploadBatch()
    {

        try {

            $this->validate([
                'upload' => 'required|file|mimes:xlsx,csv',
            ]);
            $name = 'seed' . time() . '.' . $this->upload->getClientOriginalExtension();
            $this->upload->storeAs('public/imports', $name);

            // Use storage_path to get the absolute path
            $path = storage_path('app/public/imports/' . $name);
            try {


                Excel::import(new SeedBeneficiariesImport(cacheKey: $this->importId, filePath: $path, submissionDetails: [

                    'user_id' => Auth::user()->id,
                    'batch_no' => $this->importId


                ]), $path);
                $this->checkProgress();
            } catch (ExcelValidationException $th) {

                $this->reset('upload');
                session()->flash('error', $th->getMessage());
                Log::error($th);
            }


        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }



        // Use Excel import (Assumes you have set up an Import for SeedBeneficiaries)


        session()->flash('message', 'Batch uploaded successfully.');
        $this->reset('upload'); // Clear the file input after upload
    }

    public function downloadTemplate()
    {
        // Path to your template file
        return Excel::download(new SeedBeneficiariesExport(true), 'seed_beneficiaries.xlsx');
    }

    public function mount()
    {
        $this->importId = Uuid::uuid4()->toString();
        $this->routePrefix = Route::current()->getPrefix();
    }


    public function render()
    {
        return view('livewire.other-forms.seed-beneficiaries.add');
    }
}
