<?php

namespace App\Livewire\Admin\Operations;

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
        return view('livewire.admin.operations.forms');
    }
}
