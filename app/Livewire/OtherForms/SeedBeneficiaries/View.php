<?php

namespace App\Livewire\OtherForms\SeedBeneficiaries;

use App\Models\Crop;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SeedBeneficiary;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class View extends Component
{
    use LivewireAlert;
    public $rowId;

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
    public $type_of_plot,
        $type_of_actor,
        $season_type,
        $group_name;
    public $national_id;
    public $phone_number;
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
        'sex' => 'required|string',
        'age' => 'required|integer|min:1',
        'marital_status' => 'required|string',
        'hh_head' => 'required|string',
        'household_size' => 'required|integer|min:1',
        'children_under_5' => 'required|integer|min:0',
        'variety_received' => 'required|string|max:255',
        'bundles_received' => 'required|integer|min:1',
        'national_id' => 'nullable|string|max:20',
        'phone_number' => 'nullable|max:255',
        'crop' => 'required|string|in:OFSP,Potato,Cassava',
        'type_of_actor' => 'nullable|string',
        'type_of_plot' => 'nullable|string',
        'season_type' => 'required',
        'group_name' => 'nullable|string',
    ];
    public $varieties = [];
    public $selectedVarieties = [];
    public $editVarieties = [];

    public function setData($rowId)
    {
        $this->rowId = $rowId;


        $data = SeedBeneficiary::find($this->rowId);
        $this->district = $data->district;
        $this->epa = $data->epa;
        $this->section = $data->section;
        $this->name_of_aedo = $data->name_of_aedo;
        $this->aedo_phone_number = $data->aedo_phone_number;
        $this->date = $data->date;
        $this->name_of_recipient = $data->name_of_recipient;
        $this->village = $data->village;
        $this->sex = $data->sex;
        $this->age = $data->age;
        $this->marital_status = $data->marital_status;
        $this->hh_head = $data->hh_head;
        $this->household_size = $data->household_size;
        $this->children_under_5 = $data->children_under_5;
        $this->variety_received = $data->variety_received;
        $this->bundles_received = $data->bundles_received;
        $this->national_id = $data->national_id;
        $this->phone_number = $data->phone_number;
        $this->crop = $data->crop;
        $this->editVarieties = explode(',', $data->variety_received);
        $this->type_of_actor = $data->type_of_actor;
        $this->type_of_plot = $data->type_of_plot;
        $this->season_type = $data->season_type;
        $this->group_name = $data->group_name;

        $this->getVarieties($this->crop);
        $this->dispatch('get-varieties', data: $this->varieties, variety_received: $this->editVarieties);
    }

    public function deleteDetail()
    {
        SeedBeneficiary::find($this->rowId)->delete();
        $this->dispatch('hideModal');
        session()->flash('success', 'Seed Beneficiary Record has been deleted successfully.');
    }
    public function saveChanges()
    {
        $collect = collect($this->selectedVarieties);
        if ($collect->isNotEmpty()) {
            $this->variety_received = implode(',', $collect->pluck('name')->toArray());
        } else {
            $this->variety_received = null;
        }

        try {

            $this->validate();
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        try {
            DB::beginTransaction();
            SeedBeneficiary::find($this->rowId)->update([
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
                'crop' => $this->crop,
                'user_id' => auth()->user()->id,
                'type_of_actor' => $this->type_of_actor,
                'type_of_plot' => $this->type_of_plot,
                'season_type' => $this->season_type,
                'group_name' => $this->group_name,
                'national_id' => $this->national_id,
                'phone_number' => $this->phone_number
            ]);
            $this->dispatch('hideModal');
            session()->flash('success', 'Record updated successfully.');
            DB::commit();
        } catch (\Throwable $th) {

            DB::rollBack();
            session()->flash('error', 'Something went wrong!');
            Log::error($th);
        }
    }
    public function getVarieties($crop)
    {
        if ($crop == 'OFSP') {
            $crop = 'Sweet potato';
        }
        $this->varieties = Crop::where('name', $crop)->first()->varieties->toArray();
    }
    public function updatedCrop($value)
    {

        $this->getVarieties($value);

        $this->dispatch('get-varieties', data: $this->varieties);
    }

    public function mount() {}


    public function render()
    {
        return view('livewire.other-forms.seed-beneficiaries.view');
    }
}
