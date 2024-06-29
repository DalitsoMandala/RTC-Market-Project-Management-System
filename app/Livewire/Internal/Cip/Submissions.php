<?php

namespace App\Livewire\Internal\Cip;

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

    #[On('set')]
    public function setData($id)
    {
        $this->resetErrorBag();
        $submission = Submission::find($id);
        $this->rowId = $id;
        $this->status = $submission->status === 'pending' ? null : $submission->status;
        $this->comment = $submission->comments;
    }

    public function save()
    {
        $this->validate();

        try {

            $submission = Submission::find($this->rowId);

            if ($this->status === 'approved') {

                $table = $submission->table_name;
                $decodedBatch = json_decode($submission->data, true);
                $data = [];
                foreach ($decodedBatch as $batch) {

                    $batch['created_at'] = now();
                    $batch['updated_at'] = now();
                    $data[] = $batch;
                }

                if ($submission->batch_type == 'batch') {
                    DB::table($table)->insert($data);

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
                ]);
            }

            session()->flash('success', 'Successfully updated');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
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
