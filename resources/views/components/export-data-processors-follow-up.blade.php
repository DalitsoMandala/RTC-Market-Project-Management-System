<div>


    <button type="button" name="" id="" class="btn btn-soft-dark waves-effect waves-light my-2"
        wire:click='$dispatch("export-followup")'>
        <i class="fas fa-file-excel"></i> Export
    </button>
    @php

        $prefix = Route::current()->getPrefix();

        $route = '' . $prefix . '/forms/rtc-market/rtc-production-and-marketing-form-processors/followup';
    @endphp
    <a href="{{ $route }}" class="btn btn-warning waves-effect waves-light my-2">
        <i class="bx bx-plus"></i> Add Follow up
    </a>






</div>