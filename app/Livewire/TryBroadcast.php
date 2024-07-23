<?php

namespace App\Livewire;

use App\Jobs\ExampleJob;
use App\Jobs\RandomNames;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TryBroadcast extends Component
{
    public $workerRunning = false;




    public $progress = 0;
    public $progress2 = 0;
    public $progressKey;
    public $progressKey2;
    public function mount()
    {
        $this->progressKey = 'job-progress-' . uniqid();
        $this->progressKey2 = 'job-progress-' . uniqid();


    }

    public function startJob()
    {
        dispatch(new ExampleJob($this->progressKey));

    }

    public function updateProgress()
    {

        $this->progress = Cache::get($this->progressKey, 0);

        // $this->progress2 = Cache::get($this->progressKey2, 0);

    }


    public function render()
    {
        return view('livewire.try-broadcast');
    }
}