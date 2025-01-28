<?php

namespace App\Livewire\Imports;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ImportData extends Component
{
    use LivewireAlert;
    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId;

    public function save() {}

    public function mount() {}


    public function render()
    {
        return view('livewire.imports.import-data');
    }
}
