<div>
    @php
        $queues = data_get($setUp, 'exportable.batchExport.queues', 0);
    @endphp
    @if ($queues > 0 && $showExporting)

        @if ($batchExporting && !$batchFinished)
            <div wire:poll="updateExportProgress" class="px-4 py-3 my-3 text-center rounded-md shadow-sm">
                <div>{{ trans('livewire-powergrid::datatable.export.exporting') }}</div>
                <div class="text-center rounded bg-emerald-500"
                    style="background-color: rgb(16 185 129); height: 0.25rem; width: {{ $batchProgress }}%; transition: width 300ms;">
                </div>
            </div>

            <div wire:poll="updateExportProgress" class="px-4 py-3 my-3 text-center rounded-md shadow-sm">
                <div>{{ $batchProgress }}%</div>
                <div>{{ trans('livewire-powergrid::datatable.export.exporting') }}</div>
            </div>
        @endif

        @if ($batchFinished)
            <div class="my-3">
                <p>
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseCompleted" aria-expanded="false" aria-controls="collapseCompleted">
                        ⚡ {{ trans('livewire-powergrid::datatable.export.completed') }}
                    </button>
                </p>
                <div class="collapse" id="collapseCompleted">
                    <div class="card card-body">
                        @foreach ($exportedFiles as $file)
                            <div class="w-full p-2 d-flex" style="cursor:pointer">
                                <x-livewire-powergrid::icons.download
                                    style="width: 1.5rem;
                                           margin-right: 6px;
                                           color: #2d3034;" />
                                <a wire:click="downloadExport('{{ $file }}')">
                                    {{ $file }}
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>
