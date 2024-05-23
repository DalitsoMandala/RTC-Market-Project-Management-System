<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Form;
use App\Models\Submission;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
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
    public function setData($id)
    {
        $this->resetErrorBag();
        $submission = Submission::find($id);
        $this->rowId = $id;
        $this->status = $submission->status;
        $this->comment = $submission->comments;
    }

    public function save()
    {
        $this->validate();

        try {


            Submission::find($this->rowId)->update([
                'status' => $this->status,
                'comments' => $this->comment
            ]);

            $this->alert('success', 'Successfully updated');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            dd($th);
            $this->alert('error', 'Something went wrong');
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