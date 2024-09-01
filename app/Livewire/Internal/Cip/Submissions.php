<?php

namespace App\Livewire\Internal\Cip;

use App\Models\FinancialYear;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RpmFarmerDomMarket;
use App\Models\RpmFarmerFollowUp;
use App\Models\RpmFarmerInterMarket;
use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Models\User;
use App\Notifications\SubmissionNotification;
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

        if ($submission->table_name == 'reports') {
            $uuid = $submission->batch_no;
            $reports = SubmissionReport::where('uuid', $uuid)->first();
            $json_data = json_decode($reports->data, true);
            $this->inputs = $json_data;

        }

    }

    public function save()
    {
        $this->validate();

        try {

            $submission = Submission::find($this->rowId);






            if ($this->status === 'approved') {

                $table = json_decode($submission->table_name);





                if ($submission->batch_type == 'batch') {
                    $findData = $this->checkbatch('household_rtc_consumption', $submission->batch_no);
                    if ($findData) {
                        DB::table('household_rtc_consumption')->where('uuid', $submission->batch_no)->update([
                            'status' => 'approved'
                        ]);
                    }



                    $findData = $this->checkbatch('rtc_production_farmers', $submission->batch_no);
                    if ($findData) {
                        DB::table('rtc_production_farmers')->where('uuid', $submission->batch_no)->update([
                            'status' => 'approved'
                        ]);
                    }


                    $findData = $this->checkbatch('rtc_production_processors', $submission->batch_no);
                    if ($findData) {
                        DB::table('rtc_production_processors')->where('uuid', $submission->batch_no)->update([
                            'status' => 'approved'
                        ]);
                    }

                    $findData = $this->checkbatch('attendance_registers', $submission->batch_no);
                    if ($findData) {
                        DB::table('attendance_registers')->where('uuid', $submission->batch_no)->update([
                            'status' => 'approved'
                        ]);
                    }







                }





                if ($submission->batch_type == 'aggregate') {

                    $findData = $this->checkbatch('submission_reports', $submission->batch_no);


                    if ($findData) {
                        DB::table('submission_reports')->where('uuid', $submission->batch_no)->update([
                            'status' => 'approved'
                        ]);
                    }

                }
                $submission = Submission::find($this->rowId)->update([
                    'status' => $this->status,
                    'comments' => $this->comment,
                    'is_complete' => true,
                ]);


                $submission = Submission::find($this->rowId);

                $user = User::find($submission->user_id);



                $user->notify(new SubmissionNotification(status: 'accepted', batchId: $submission->batch_no));




            } else {






                if ($submission->batch_type == 'batch') {
                    $findData = $this->checkbatch('household_rtc_consumption', $submission->batch_no);
                    if ($findData) {
                        DB::table('household_rtc_consumption')->where('uuid', $submission->batch_no)->delete();
                    }

                    $findData = $this->checkbatch('attendance_registers', $submission->batch_no);
                    if ($findData) {
                        DB::table('attendance_registers')->where('uuid', $submission->batch_no)->delete();
                    }

                    $findData = $this->checkbatch('rtc_production_farmers', $submission->batch_no);
                    if ($findData) {
                        DB::table('rtc_production_farmers')->where('uuid', $submission->batch_no)->delete();
                    }


                    $findData = $this->checkbatch('rtc_production_processors', $submission->batch_no);
                    if ($findData) {
                        DB::table('rtc_production_processors')->where('uuid', $submission->batch_no)->delete();
                    }


                }




                if ($submission->batch_type == 'aggregate') {

                    $findData = $this->checkbatch('submission_reports', $submission->batch_no);


                    if ($findData) {
                        DB::table('submission_reports')->where('uuid', $submission->batch_no)->delete();
                    }

                }

                $submission = Submission::find($this->rowId)->update([
                    'status' => $this->status,
                    'comments' => $this->comment,
                    'is_complete' => true,
                ]);








                $submission = Submission::find($this->rowId);

                $user = User::find($submission->user_id);


                $user->notify(new SubmissionNotification('denied', 'Batch NO. ' . $submission->batch_no . ' : ' . $this->comment));



            }


            $this->dispatch('hideModal');
            $this->dispatch('refresh');
            session()->flash('success', 'Successfully updated');

        } catch (\Throwable $th) {
            dd($th);
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
            session()->flash('error', 'Something went wrong');

            Log::channel('system_log')->error($th->getMessage());
        }



    }

    public function checkbatch($table, $uuid)
    {
        return DB::table($table)->where('uuid', $uuid)->exists();

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

    public function populateRtcProducers($data)
    {
        $idMappings = [];
        $highestId = RtcProductionProcessor::max('id');
        foreach ($data['main'] as $mainSheet) {
            $highestId++;

            $mainSheet['is_registered'] = $mainSheet['is_registered'] === 'YES' ? true : false;

            $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] === 'YES' ? true : false;
            $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] === 'YES' ? true : false;
            $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] === 'YES' ? true : false;
            $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] === 'YES' ? true : false;

            $idMappings[$mainSheet['#']] = $highestId;
            unset($mainSheet['#']);
            RtcProductionProcessor::create($mainSheet);

        }

        foreach ($data['followup'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_processor_id']];
            $mainSheet['rpm_processor_id'] = $newId;

            $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] === 'YES' ? true : false;
            $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] === 'YES' ? true : false;
            $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] === 'YES' ? true : false;
            $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] === 'YES' ? true : false;
            $mainTable = RpmProcessorFollowUp::create($mainSheet);

            // follow up data

        }

        foreach ($data['agreement'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_processor_id']];
            $mainSheet['rpm_processor_id'] = $newId;
            $mainTable = RpmProcessorConcAgreement::create($mainSheet);

            // conc agreement

        }

        foreach ($data['market'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_processor_id']];
            $mainSheet['rpm_processor_id'] = $newId;
            $mainTable = RpmProcessorDomMarket::create($mainSheet);

            // dom market

        }

        foreach ($data['intermarket'] as $mainSheet) {
            $newId = $idMappings[$mainSheet['rpm_processor_id']];
            $mainSheet['rpm_processor_id'] = $newId;
            $mainTable = RpmProcessorInterMarket::create($mainSheet);

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
