<div>


    @if (!$showContent)
        <div x-data x-init="() => {
            setTimeout(() => {
        
                $wire.dispatch('showCharts');
            }, 5000)
        }">



            @include('placeholders.dashboard2')
        </div>
    @else
        <livewire:charts-view-2 />
    @endif
</div>
