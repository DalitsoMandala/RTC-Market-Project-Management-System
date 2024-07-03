<?php

namespace App\Livewire\Internal\Cip;

use App\Models\RpmFarmerConcAgreement;
use App\Models\RpmFarmerDomMarket;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Submissions extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $status;
    #[Validate('required')]
    public $comment;
    public $rowId;

    public $inputs = [];

    #[On('set')]
    public function setData($id)
    {
        $this->resetErrorBag();
        $submission = Submission::find($id);
        $this->rowId = $id;
        $this->status = $submission->status === 'pending' ? null : $submission->status;
        $this->comment = $submission->comments;
        $json_data = json_decode($submission->data, true);
        $this->inputs = $json_data;
    }

    public function save()
    {
        $this->validate();

        try {

            $submission = Submission::find($this->rowId);

            if ($this->status === 'approved') {

                $table = json_decode($submission->table_name);


                $decodedBatch = json_decode($submission->data, true);


                if ($submission->batch_type == 'batch') {
                    if (count($table) == 1) {
                        $data = [];

                        foreach ($decodedBatch as $batch) {

                            $batch['created_at'] = now();
                            $batch['updated_at'] = now();
                            $data[] = $batch;
                        }
                        if ($table[0] == 'household_rtc_consumption') {

                            DB::table($table[0])->insert($data);
                        }

                    } else if (count($table) > 0) {

                        if ($table[0] == 'rtc_production_farmers') {
                            $this->populateRtcFarmers($decodedBatch);
                        }

                    }




                }

                Submission::find($this->rowId)->update([
                    'status' => $this->status,
                    'comments' => $this->comment,
                    'is_complete' => true,
                ]);

            }

            if ($this->status === 'denied') {

                $submission->update([
                    'is_complete' => true,
                    'status' => $this->status,
                ]);
            }

            session()->flash('success', 'Successfully updated');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {

            session()->flash('error', 'Something went wrong');

            Log::channel('system_log')->error($th->getMessage());
        }

        $this->dispatch('hideModal');
        $this->dispatch('refresh');
    }


    public function populateRtcFarmers($data)
    {
        $idMappings = [];
        $highestId = RtcProductionFarmer::max('id');
        foreach ($data['main'] as $mainSheet) {
            $highestId++;

            $mainSheet['is_registered'] = $mainSheet['is_registered'] === 'YES' ? true : false;
            $mainSheet['is_registered_seed_producer'] = $mainSheet['is_registered_seed_producer'] === 'YES' ? true : false;
            $mainSheet['uses_certified_seed'] = $mainSheet['uses_certified_seed'] === 'YES' ? true : false;
            $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] === 'YES' ? true : false;
            $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] === 'YES' ? true : false;
            $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] === 'YES' ? true : false;
            $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] === 'YES' ? true : false;

            $idMappings[$mainSheet['#']] = $highestId;
            unset($mainSheet['#']);
            RtcProductionFarmer::create($mainSheet);

        }

        foreach ($data['followup'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_farmer_id']];
            $mainSheet['rpm_farmer_id'] = $newId;
            $mainSheet['is_registered_seed_producer'] = $mainSheet['is_registered_seed_producer'] === 'YES' ? true : false;
            $mainSheet['uses_certified_seed'] = $mainSheet['uses_certified_seed'] === 'YES' ? true : false;
            $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] === 'YES' ? true : false;
            $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] === 'YES' ? true : false;
            $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] === 'YES' ? true : false;
            $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] === 'YES' ? true : false;
            $mainTable = RpmFarmerFollowUp::create($mainSheet);

            // follow up data

        }

        foreach ($data['agreement'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_farmer_id']];
            $mainSheet['rpm_farmer_id'] = $newId;
            $mainTable = RpmFarmerConcAgreement::create($mainSheet);

            // conc agreement

        }

        foreach ($data['market'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_farmer_id']];
            $mainSheet['rpm_farmer_id'] = $newId;
            $mainTable = RpmFarmerDomMarket::create($mainSheet);

            // dom market

        }

        foreach ($data['intermarket'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_farmer_id']];
            $mainSheet['rpm_farmer_id'] = $newId;
            $mainTable = RpmFarmerInterMarket::create($mainSheet);

            // inter market

        }

    }
    public function saveAGG()
    {
        $this->validate();

        try {

            $submission = Submission::find($this->rowId);

            if ($this->status === 'approved') {






                Submission::find($this->rowId)->update([
                    'status' => $this->status,
                    'comments' => $this->comment,
                    'is_complete' => true,
                ]);

            }

            if ($this->status === 'denied') {

                $submission->update([
                    'is_complete' => true,
                    'status' => $this->status,
                ]);
            }

            session()->flash('success', 'Successfully updated');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            dd($th);
            session()->flash('error', 'Something went wrong');

            Log::error($th);
        }

        $this->dispatch('hideModal');
        $this->reset();
    }


    public function render()
    {
        return view('livewire.internal.cip.submissions');
    }
}