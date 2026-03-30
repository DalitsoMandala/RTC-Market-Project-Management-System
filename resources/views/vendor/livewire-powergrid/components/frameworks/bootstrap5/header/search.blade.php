<div class="col-12" style="text-align: right;">
    <label class="col-12 col-sm-8 ">
        @if (data_get($setUp, 'header.searchInput'))
            <div class="input-group w-100" style="position: relative;">
    <span class="input-group-text">
        <svg width="16" height="16" fill="currentColor"
            class="{{ data_get($theme, 'searchBox.iconSearchClass') }}"
            style="{{ data_get($theme, 'searchBox.iconSearchStyle') }}" viewBox="0 0 16 16">
            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"></path>
        </svg>
    </span>

    <input wire:model.live.debounce.1000ms="search" type="text"
        class="{{ data_get($theme, 'searchBox.inputClass') }}"
        style="{{ data_get($theme, 'searchBox.inputStyle') }}"
        placeholder="{{ trans('livewire-powergrid::datatable.placeholders.search') }}">

    @if(filled($search))
        <button
            wire:click="$set('search', '')"
            type="button"
            class="px-1 bg-transparent btn custom-tooltip" title="Clear Search"
            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); z-index: 100; border: none; padding: 0;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg text-secondary" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
            </svg>
        </button>
    @endif
</div>
        @endif
    </label>
</div>
