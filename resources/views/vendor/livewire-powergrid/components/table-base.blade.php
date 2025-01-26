@props([
    'theme' => null,
    'readyToLoad' => false,
    'items' => null,
    'lazy' => false,
    'tableName' => null,
])
@php
    $baseTableClass = str_replace('table-bordered', '', $theme['table']['tableClass']);
    $headerTableClass = $theme['table']['theadClass'] . ' table-warning';
@endphp

<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <table id="table_base_{{ $tableName }}" class="table power-grid-table {{ $baseTableClass }}"
        style="{{ data_get($theme, 'tableStyle') }} ">
        <thead class="{{ $headerTableClass }}" style="{{ data_get($theme, 'table.theadStyle') }} ">
            {{ $header }}
        </thead>
        @if ($readyToLoad)
            <tbody class="{{ data_get($theme, 'table.tbodyClass') }}" style="{{ data_get($theme, 'table.tbodyStyle') }} ">
                {{ $body }}
            </tbody>
        @else
            <tbody class="{{ data_get($theme, 'table.tbodyClass') }}"
                style="{{ data_get($theme, 'table.tbodyStyle') }} ">
                {{ $loading }}
            </tbody>
        @endif
    </table>

    {{-- infinite pagination handler --}}
    @if ($this->canLoadMore && $lazy)
        <div class="items-center justify-center" wire:loading.class="flex" wire:target="loadMore">
            @include(powerGridThemeRoot() . '.header.loading')
        </div>

        <div x-data="pgLoadMore"></div>
    @endif
</div>
