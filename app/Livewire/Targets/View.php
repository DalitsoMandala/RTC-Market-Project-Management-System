<?php

namespace App\Livewire\Targets;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class View extends Component
{
    use LivewireAlert;

    public $routePrefix;
    public function save(
    ) {


    }

    public function mount()
    {
        $this->routePrefix = Route::current()->getPrefix();

    }


    public function render()
    {
        return view('livewire.targets.view');
    }
}
