<?php

namespace App\Livewire\Internal\Cip\Markets;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Models\Role;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ManageData extends Component
{
    use LivewireAlert;
public $routePrefix;



    public function mount() {
        $this->routePrefix = Route::current()->getPrefix();
    }


    public function render()
    {
        return view('livewire.internal.cip.markets.manage-data');
    }
}
