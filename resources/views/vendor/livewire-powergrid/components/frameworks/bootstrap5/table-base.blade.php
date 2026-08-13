<div @if ($deferLoading) wire:init="fetchDatasource" @endif>
    <div class="col-md-12">
        @include(data_get($theme, 'layout.header'), [
            'enabledFilters' => $enabledFilters,
        ])
    </div>
    <div class="{{ data_get($theme, 'table.divClass') }} border rounded-3 "
        style="{{ data_get($theme, 'table.divStyle') }}">
        @include($table)
    </div>
    <div class="row">
        <div class="overflow-auto col-12">
            @include(data_get($theme, 'footer.view'), ['theme' => $theme])
        </div>
    </div>
</div>
