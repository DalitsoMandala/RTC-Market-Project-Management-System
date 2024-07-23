<div>
    <div class="container-fluid">
        <div>
            <button wire:click="startWorker" {{ $workerRunning ? 'disabled' : '' }}>Start Worker</button>
            <button wire:click="stopWorker" {{ !$workerRunning ? 'disabled' : '' }}>Stop Worker</button>

            <div>
                Worker is {{ $workerRunning ? 'running' : 'stopped' }}.
            </div>

            <button wire:click="startJob">Start Job</button>
            <div wire:poll.1000ms="updateProgress">
                Progress: {{ $progress }}%
            </div>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;"
                    aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>

            <div wire:poll.1000ms="updateProgress">
                Progress: {{ $progress2 }}%
            </div>
            <div class="progress">
                <div class="progress-bar" role="progressbar" style="width: {{ $progress2 }}%;"
                    aria-valuenow="{{ $progress2 }}" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
        </div>



    </div>

</div>
