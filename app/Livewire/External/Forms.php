<?php

namespace App\Livewire\External;

use App\Models\Organisation;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Forms extends Component
{
    use LivewireAlert;

    public $organisation;
    public $rowId;

    public function mount()
    {
        $user = Auth::user();
        $organisation = $user->organisation;

        $this->organisation = $organisation->name;
    }

    public function render()
    {
        return view('livewire.external.forms');
    }
}
