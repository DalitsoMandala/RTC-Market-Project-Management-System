<?php

namespace App\Livewire\Admin\System;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Organisation;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class OrganisationList extends Component
{
    use LivewireAlert;
    public $rowId;
    #[Validate('required')]
    public $name;
    #[Validate('required|in:CDMS')]
    public $confirm;

    #[On('showModal')]
    public function setData($rowId)
    {
        $this->rowId = $rowId;
        $data = Organisation::find($this->rowId);
        $this->name = $data->name;
    }
    public function save()
    {
        $this->validate();

        try {
            Organisation::find($this->rowId)->update([
                'name' => $this->name
            ]);
            $this->dispatch('hideModal');
            $this->alert('success', 'Organisation updated successfully');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }

    }

    public function mount()
    {

    }


    public function render()
    {
        return view('livewire.admin.system.organisation-list');
    }
}
