<?php

namespace App\Livewire\Internal\Cip\Markets;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ManageData extends Component
{
    use LivewireAlert;




    public function mount() {}


    public function render()
    {
        return view('livewire.internal.cip.markets.manage-data');
    }
}
