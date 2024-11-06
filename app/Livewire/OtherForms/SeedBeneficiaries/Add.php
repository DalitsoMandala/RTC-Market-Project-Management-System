<?php

namespace App\Livewire\OtherForms\SeedBeneficiaries;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SeedBeneficiary;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SeedBeneficiariesExport;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Add extends Component
{
    use LivewireAlert;
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
            ]);

            session()->flash('success', 'Seed Beneficiary added successfully.');

        } catch (\Throwable $th) {
            dd($th);

            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());
        }


    }

    public function uploadBatch()
    {
        $this->validate([
            'upload' => 'required|file|mimes:xlsx,csv',
        ]);

        // Use Excel import (Assumes you have set up an Import for SeedBeneficiaries)


        session()->flash('message', 'Batch uploaded successfully.');
        $this->reset('upload'); // Clear the file input after upload
    }

    public function downloadTemplate()
    {
        // Path to your template file
        return Excel::download(new SeedBeneficiariesExport, 'seed_beneficiaries.xlsx');
    }

    public function mount()
    {

    }


    public function render()
    {
        return view('livewire.other-forms.seed-beneficiaries.add');
    }
}
