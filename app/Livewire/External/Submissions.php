<?php

namespace App\Livewire\External;

use Livewire\Component;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\SubmissionReport;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Submissions extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId;

    public $status;

    public $comment;
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

        $this->resetErrorBag();
        try {

            $this->alert('success', 'Successfully updated');

        } catch (\Throwable $th) {
            $this->alert('error', 'Something went wrong');
            Log::error($th);
        }
        $this->reset();
    }

    public function render()
    {
        return view('livewire.external.submissions');
    }
}
