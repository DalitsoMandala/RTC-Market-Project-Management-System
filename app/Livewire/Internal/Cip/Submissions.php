<?php

namespace App\Livewire\Internal\Cip;

use App\Models\User;
use Livewire\Component;
use App\Models\MarketData;
use App\Models\Submission;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Models\RpmFarmerFollowUp;
use Livewire\Attributes\Validate;
use App\Models\ProgressSubmission;
use App\Models\RpmFarmerDomMarket;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;
use App\Models\MarketDataSubmission;
use App\Models\RpmFarmerInterMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorDomMarket;
use Illuminate\Support\Facades\Route;
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
    #[Validate('required_if:status,denied|max:255')]
    public $comment;
    public $rowId;

    public $inputs = [];
    public $disable = false;
    public $statusSet = false;
    public $tableName;
    public $isManager = false;

    public $confirmDeleteProgress;
    public function messages()
    {
        return [
            'comment.required_if' => 'A comment is required when the status is disapproved.',
        ];
    }
    #[On('set')]
    public function setData($id)
    {
        $this->resetErrorBag();
        $submission = Submission::find($id);
        $this->rowId = $id;
        $this->status = $submission->status === 'pending' ? null : $submission->status;
        $this->comment = $submission->comments;
        $this->statusSet = $submission->status === 'pending' ? false : true;
        if ($submission->table_name == 'reports') {
            $uuid = $submission->batch_no;
            $reports = SubmissionReport::where('uuid', $uuid)->first();
            $json_data = json_decode($reports->data, true);
            $this->inputs = $json_data;
        }
        $this->tableName = $submission->table_name;
        $userId = $submission->user_id;
        $user = User::find($userId);
        $this->isManager = $user->hasAnyRole('manager') || $user->hasAnyRole('admin'); //
    }


    #[On('setMarket')]
    public function setDataMarket($id)
    {
        $this->resetErrorBag();
        $submission = MarketDataSubmission::find($id);
        $this->rowId = $id;
        $this->status = $submission->status === 'pending' ? null : $submission->status;
        $this->statusSet = $submission->status === 'pending' ? false : true;
        $this->tableName = $submission->table_name;
        $userId = $submission->submitted_user_id;
        $user = User::find($userId);
        $this->isManager = $user->hasAnyRole('manager') || $user->hasAnyRole('admin'); //
    }

    private function getLink($submission, $type = null)
    {
        $user_id = null;

        if ($type == '#market-submission' || $type == '#gross-submission') {
            $user_id = $submission->submitted_user_id;
        } else {
            $user_id = $submission->user_id;
        }

        $link = '';


        if (User::find($user_id)->hasAnyRole('manager')) {
            $link = route('cip-submissions', [
                'batch' => $submission->batch_no
            ]) . $type;
        } else if (User::find($user_id)->hasAnyRole('staff')) {
            $link = route('cip-staff-submissions', [
                'batch' => $submission->batch_no
            ]) . $type;
        } else if (User::find($user_id)->hasAnyrole('admin')) {
            $link = route('admin-submissions', [
                'batch' => $submission->batch_no
            ]) . $type;
        } elseif (User::find($user_id)->hasAnyrole('enumarator')) {
            $link = route('enumerator-submissions', [
                'batch' => $submission->batch_no
            ]) . $type;
        } else {

            $link = route('external-submissions', [
                'batch' => $submission->batch_no
            ]) . $type;
        }


        return $link;
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
            $user = User::find($submission->user_id);
                $link = $this->getLink($submission,'#batch-submission');
            // Perform actions based on status
            if ($this->status === 'approved') {
                $this->approveSubmission($submission, [$this->tableName]);
                // Dispatch notification using Bus::chain
                Bus::chain([
                    fn() => $user->notify(new SubmissionNotification(
                        status: $this->status,
                        denialMessage: null,
                        batchId: $submission->batch_no,
                        link: $link
                    )),
                ])->dispatch();
            } else {
                $this->denySubmission($submission, [$this->tableName]);



                Bus::chain([
                    fn() => $user->notify(new SubmissionNotification(
                        status: 'denied',
                        denialMessage: $this->comment,
                        batchId: $submission->batch_no,

                        link: $link
                    )),
                ])->dispatch();
            }




            session()->flash('success', 'Successfully updated');
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
            session()->flash('error', 'Something went wrong');
        }
    }

    public function approveBatchSubmission()
    {

        $this->status = 'approved';
        $this->save();
    }

    public function disapproveBatchSubmission()
    {
        $this->status = 'denied';
        $this->save();
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
            DB::table($submission->table_name)->where('uuid', $submission->batch_no)->delete();
            $submission->delete();
            session()->flash('success', 'Successfully deleted batch');
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            session()->flash('error', 'Something went wrong');

            Log::error($th);
        }
    }

    public function deleteMarketBatch()
    {

        try {
            $submission = MarketDataSubmission::findOrFail($this->rowId);
            DB::table($submission->table_name)->where('uuid', $submission->batch_no)->delete();
            $submission->delete();
            session()->flash('success', 'Successfully deleted market data');
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            session()->flash('error', 'Something went wrong');

            Log::error($th);
        }
    }
    public function deleteProgress()
    {


        try {

            $this->validate([
                'confirmDeleteProgress' => 'required|in:delete'
            ], [
                'confirmDeleteProgress.required' => 'Please confirm deletion',
                'confirmDeleteProgress.in' => 'The value must be "delete"',
            ]);
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }
        try {
            $submission = ProgressSubmission::findOrFail($this->rowId);
            DB::table($submission->table_name)->where('uuid', $submission->batch_no)->delete();
            $submission->delete();
            session()->flash('success', 'Successfully deleted progress summary');
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



    public function ApproveMarketSubmission()
    {
        $this->status = 'approved';

        try {
            # code...
            $submission = MarketDataSubmission::findOrFail($this->rowId);
            $submission->update([

                'status' => 'approved',

            ]);

            $uuid = $submission->batch_no;
            MarketData::where('uuid', $uuid)->update([
                'status' => 'approved',
            ]);
            session()->flash('success', 'Successfully approved market data submission');
            $user = User::find($submission->submitted_user_id);

            $link = $this->getLink($submission, '#market-submission');

            Bus::chain([

                fn() => $user->notify(new SubmissionNotification(
                    'approved',
                    null,
                    $submission->batch_no,
                    $link
                )),
            ])->dispatch();
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $e) {
            Log::error($e);
        }
    }

    public function DisapproveMarketSubmission()
    {
        $this->status = 'denied';
        try {
            $this->validate();
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        try {
            $submission = MarketDataSubmission::findOrFail($this->rowId);
            $submission->update([

                'status' => 'denied',
                'comments' => $this->comment

            ]);

            $uuid = $submission->batch_no;
            //delete all
            MarketData::where('uuid', $uuid)->delete();


            session()->flash('success', 'Successfully disapproved market data submission');
            $user = User::find($submission->submitted_user_id);

            $link = $this->getLink($submission, '#market-submission');
            Bus::chain([
                fn() => $user->notify(new SubmissionNotification(
                    'denied',
                    $submission->batch_no,
                    $this->comment,
                    $link,
                )),
            ])->dispatch();
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $e) {
            Log::error($e);
        }
    }
    public function ApproveAggregateSubmission()
    {
        $this->status = 'approved';

        try {
            # code...
            $submission = Submission::findOrFail($this->rowId);
            $submission->update([
                'comments' => '',
                'status' => 'approved',
                'is_complete' => true,
            ]);

            $uuid = $submission->batch_no;
            $reports = SubmissionReport::where('uuid', $uuid)->first();
            $reports->update([
                'status' => 'approved',
            ]);
            session()->flash('success', 'Successfully approved aggregate submission');
            $user = User::find($submission->user_id);
            $link = $this->getLink($submission, '#aggregate-submission');
            Bus::chain([

                fn() => $user->notify(new SubmissionNotification(
                    'approved',
                    null,
                    $submission->batch_no,
                    $link
                )),
            ])->dispatch();
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $e) {
            Log::error($e);
        }
    }

    public function DisapproveAggregateSubmission()
    {
        $this->status = 'denied';
        try {

            $this->validate();
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        try {
            $submission = Submission::findOrFail($this->rowId);
            $submission->update([
                'comments' => $this->comment,
                'status' => 'denied',
                'is_complete' => true,
            ]);

            $uuid = $submission->batch_no;
            $reports = SubmissionReport::where('uuid', $uuid)->first();
            $reports->update([
                'status' => 'denied',
            ]);
            session()->flash('success', 'Successfully disapproved aggregate submission');
            $user = User::find($submission->user_id);

            $link = $this->getLink($submission, '#aggregate-submission');
            Bus::chain([
                fn() => $user->notify(new SubmissionNotification(
                    'denied',
                    $submission->batch_no,
                    $this->comment,
                    $link,
                )),
            ])->dispatch();
            $this->dispatch('hideModal');
            $this->dispatch('refresh');
        } catch (\Throwable $e) {
            Log::error($e);
        }
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
        $batch = \App\Models\Submission::where('batch_type', 'batch')
            ->where('status', 'pending');
        $manual = \App\Models\Submission::where('batch_type', 'manual')
            ->where('status', 'pending');
        $aggregate = \App\Models\Submission::where('batch_type', 'aggregate')
            ->where('status', 'pending');
        $market = \App\Models\MarketDataSubmission::where('status', 'pending');
        $pendingJob = JobProgress::where('user_id', auth()->user()->id)
            ->where('status', 'processing')->count();

        if (User::find(auth()->user()->id)->hasAnyRole('external')) {
            $batch = $batch->where('user_id', auth()->user()->id);
            $manual = $manual->where('user_id', auth()->user()->id);
            $aggregate = $aggregate->where('user_id', auth()->user()->id);
        }

         if (User::find(auth()->user()->id)->hasAnyRole('enumerator')) {
            $market = $market->where('submitted_user_id', auth()->user()->id);
        }
        return view('livewire.internal.cip.submissions', [
            'batch' => $batch->count(),
            'manual' => $manual->count(),
            'aggregate' => $aggregate->count(),
            'pendingJob' => $pendingJob,
            'market' => $market->count(),


        ]);
    }
}
