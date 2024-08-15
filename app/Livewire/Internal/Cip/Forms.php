<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Form;
use App\Models\Indicator;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Forms extends Component
{


    public function render()
    {
        return view('livewire.internal.cip.forms');
    }
}
