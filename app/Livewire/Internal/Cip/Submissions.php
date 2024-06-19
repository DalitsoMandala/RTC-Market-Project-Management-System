<?php

namespace App\Livewire\Internal\Cip;

use App\Models\HouseholdRtcConsumption;
use App\Models\HrcLocation;
use App\Models\HrcMainFood;
use App\Models\Submission;
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

            Submission::find($this->rowId)->update([
                'status' => $this->status,
                'comments' => $this->comment,
            ]);

            $submission = Submission::find($this->rowId);

            if ($this->status === 'approved' && $submission->is_complete === 0) {

                $decodedBatch = json_decode($submission->data, true);
                foreach ($decodedBatch as $data) {
                    $location_data = $data['location_data'];
                    $main_food_data = $data['main_food_data'];
                    unset($data['location_data']);
                    unset($data['main_food_data']);
                    $data['uuid'] = $submission['batch_no'];
                    $location = HrcLocation::create($location_data);
                    $data['location_id'] = $location->id;
                    $insert = HouseholdRtcConsumption::create($data);
                    foreach ($main_food_data as $food) {
                        HrcMainFood::create([
                            'name' => $food['name'],
                            'hrc_id' => $insert->id,
                        ]);

                    }

                }

                $submission->update([
                    'is_complete' => true,
                ]);
            }

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
