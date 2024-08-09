<?php

namespace App\Livewire\External;

use App\Models\Indicator;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ViewIndicator extends Component
{
    use LivewireAlert;
    public $indicator_name, $indicator_no, $project_name, $indicator_id, $project_id;

    public function mount(Indicator $id)
    {
        $this->indicator_name = $id->indicator_name;
        $this->indicator_id = $id->id;
        $this->project_id = $id->project->id;
        $this->indicator_no = $id->indicator_no;
        $this->project_name = strtoupper($id->project->name);
    }

    public function render()
    {
        return view('livewire.external.view-indicator');
    }
}
