<div class="dropdown .3">
      <button title="Export File" class="border custom-tooltip btn btn-light d-flex align-items-center" type="button"
        data-bs-toggle="dropdown" aria-expanded="false">
        <img src="{{ asset('assets/images/icons/sheet.png') }}" width="20" height="20" /> <i
            class="opacity-75 ms-2 bx bx-arrow-to-top text-success"></i>
    </button>
    <ul x-data="{countChecked: @entangle('checkboxValues').live}" class="dropdown-menu">
        @if (in_array('xlsx', data_get($setUp, 'exportable.type')))
            <li class="d-flex">
                <div class="dropdown-item">
                    <span>@lang('XLSX')</span>

                    <a
                        class="text-muted small"
                        wire:click.prevent="exportToXLS"
                        href="#"
                    >
                        <span class="export-count text-muted small">({{ $this->total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </a>
                    @if ($checkbox)
                        /
                        <a
                            class="text-muted small"
                            wire:click.prevent="exportToXLS(true)"
                            x-bind:disabled="countChecked.length === 0"
                            href="#"
                        >
                            (<span class="text-muted small" x-text="countChecked.length"></span>) @lang('livewire-powergrid::datatable.labels.selected')
                        </a>
                    @endif
                </div>
            </li>
        @endif
        @if (in_array('csv', data_get($setUp, 'exportable.type')))
            <li class="d-flex">
                <div class="dropdown-item">
                    <span>@lang('Csv')</span>
                    <a
                        class="text-muted small"
                        wire:click.prevent="exportToCsv"
                        href="#"
                    >
                        <span class="export-count text-muted small">({{ $this->total }})</span>
                        @if (count($enabledFilters) === 0)
                            @lang('livewire-powergrid::datatable.labels.all')
                        @else
                            @lang('livewire-powergrid::datatable.labels.filtered')
                        @endif
                    </a>
                    @if ($checkbox)
                        /
                        <a
                            class="text-muted small"
                            wire:click.prevent="exportToCsv(true)"
                            x-bind:disabled="countChecked.length === 0"
                            href="#"
                        >
                            (<span class="text-muted small" x-text="countChecked.length"></span>) @lang('livewire-powergrid::datatable.labels.selected')
                        </a>
                    @endif
                </div>
            </li>
        @endif
    </ul>
</div>
