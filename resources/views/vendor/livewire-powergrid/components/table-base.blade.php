@props([
    'theme' => null,
    'readyToLoad' => false,
    'items' => null,
    'lazy' => false,
    'tableName' => null,
])

<style>
    .table-secondary {

        --bs-table-bg: rgb(241 241 241/1) !important;
        --bs-table-border-color: rgb(241 241 241/1) !important;
    }
</style>
@php
    $baseTableClass = str_replace('table-bordered', '', $theme['table']['tableClass']);
    $baseTableClass = str_replace('table-striped', '', $baseTableClass);
    $headerTableClass = $theme['table']['theadClass'] . ' table-secondary grayColor';

@endphp

<div @isset($this->setUp['responsive']) x-data="pgResponsive" @endisset>
    <table id="table_base_{{ $tableName }}" class="table power-grid-table {{ $baseTableClass }}"
        style="{{ data_get($theme, 'tableStyle') }} font-size: 0.75rem;">
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
