<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Indicator;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Indicators extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $indicator;
    public $rowId;

    public function setData($id)
    {
        $this->resetErrorBag();
        $indicator = Indicator::find($id);
        $this->rowId = $id;
        $this->indicator = $indicator->indicator_name;
    }

    public function save()
    {
        $this->dispatch('hideModal');
        try {

            Indicator::find($this->rowId)->update([
                'indicator_name' => $this->indicator,
            ]);

            $this->alert('success', 'Successfully updated');
$this->dispatch('refresh');
        } catch (\Throwable $th) {
            $this->alert('danger', 'Something went wrong');
            Log::error($th);
        }


        $this->reset();
    }
    public function render()
    {
        return view('livewire.internal.cip.indicators');
    }
}