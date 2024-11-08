<?php

namespace App\Livewire\Internal\Cip;

use App\Models\User;
use Livewire\Component;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Models\RpmFarmerFollowUp;
use Livewire\Attributes\Validate;
use App\Models\RpmFarmerDomMarket;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Models\RpmFarmerInterMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmFarmerConcAgreement;
use App\Models\RtcProductionProcessor;
use App\Models\RpmProcessorInterMarket;
use App\Models\RpmProcessorConcAgreement;
use App\Notifications\SubmissionNotification;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Submissions extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $status;
    #[Validate('required')]
    public $comment;
    public $rowId;

    public $inputs = [];
    public $disable = false;

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
        try {
            $this->validate();
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        try {
            $submission = Submission::findOrFail($this->rowId);
            $tables = [
                'household_rtc_consumption',
                'rtc_production_farmers',
                'rtc_production_processors',
                'attendance_registers'
            ];

            // Perform actions based on status
            if ($this->status === 'approved') {
                $this->approveSubmission($submission, $tables);
            } else {
                $this->denySubmission($submission, $tables);
            }

            // Dispatch notification using Bus::chain
            $user = User::find($submission->user_id);
            Bus::chain([
                fn() => $user->notify(new SubmissionNotification(status: $this->status, batchId: $submission->batch_no)),
            ])->dispatch();

            $this->disable = false;
            session()->flash('success', 'Successfully updated');
            $this->dispatch('hideModal');
            $this->dispatch('refresh');

        } catch (\Throwable $th) {
            Log::channel('system_log')->error($th->getMessage());
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
            session()->flash('error', 'Something went wrong');
        }
    }

    protected function approveSubmission($submission, $tables)
    {
        if ($submission->batch_type == 'batch') {
            foreach ($tables as $table) {
                if ($this->checkbatch($table, $submission->batch_no)) {
                    DB::table($table)->where('uuid', $submission->batch_no)->update(['status' => 'approved']);
                }
            }
        } elseif ($submission->batch_type == 'aggregate' && $this->checkbatch('submission_reports', $submission->batch_no)) {
            DB::table('submission_reports')->where('uuid', $submission->batch_no)->update(['status' => 'approved']);
        }

        $submission->update([
            'status' => $this->status,
            'comments' => $this->comment,
            'is_complete' => true,
        ]);
    }

    protected function denySubmission($submission, $tables)
    {
        if ($submission->batch_type == 'batch') {
            foreach ($tables as $table) {
                if ($this->checkbatch($table, $submission->batch_no)) {
                    DB::table($table)->where('uuid', $submission->batch_no)->delete();
                }
            }
        } elseif ($submission->batch_type == 'aggregate' && $this->checkbatch('submission_reports', $submission->batch_no)) {
            DB::table('submission_reports')->where('uuid', $submission->batch_no)->delete();
        }

        $submission->update([
            'status' => $this->status,
            'comments' => $this->comment,
            'is_complete' => true,
        ]);
    }
    public function deleteBatch()
    {

        try {
            $submission = Submission::findOrFail($this->rowId);
            $tables = [
                'household_rtc_consumption',
                'rtc_production_farmers',
                'rtc_production_processors',
                'attendance_registers',

            ];
            if ($submission->batch_type == 'batch') {
                foreach ($tables as $table) {
                    if ($this->checkbatch($table, $submission->batch_no)) {
                        DB::table($table)->where('uuid', $submission->batch_no)->delete();
                    }
                }
            }

            $submission->delete();
            session()->flash('success', 'Successfully deleted batch');
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            session()->flash('error', 'Something went wrong');

            Log::error($th);
        }

    }


    public function setStatus($value)
    {
        $this->disable = true;
        $this->status = $value;
        $this->save();
    }



    public function checkbatch($table, $uuid)
    {
        return DB::table($table)->where('uuid', $uuid)->exists();
    }

    public function saveData()
    {
        $this->validate();

        try {
            $submission = Submission::find($this->rowId);
            $submission->update([
                'data' => json_encode($this->inputs),
            ]);
            session()->flash('success', 'Successfully update data');
            $this->dispatch('hideModal');
        } catch (\Throwable $th) {
            session()->flash('error', 'Something went wrong');

            Log::error($th);
        }
    }

    public function deleteAGG()
    {

        try {
            $submission = Submission::findOrFail($this->rowId);
            $submission->delete();
            $reports = SubmissionReport::where('uuid', $submission->batch_no)->first();
            $reports->delete();
            session()->flash('success', 'Successfully deleted');
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
            session()->flash('error', 'Something went wrong');

            Log::error($th);
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
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
            session()->flash('success', 'Successfully updated');

        } catch (\Throwable $th) {
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
            session()->flash('error', 'Something went wrong');

            Log::error($th);
        }


    }


    public function render()
    {
        return view('livewire.internal.cip.submissions');
    }
}
