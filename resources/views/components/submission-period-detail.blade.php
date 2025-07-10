<div>

    <!-- I begin to speak only when I am certain what I will say is not better left unsaid. - Cato the Younger -->
    <div class="row">
        <div class="col-12">


            <div wire:ignore>
                @php

                    $route = Route::current()->getPrefix();
                @endphp
            </div>

            <livewire:tables.submission-period-form-table :submissionPeriodRow="$row->toArray()['model_data']" :currentRoutePrefix="$route" />

        </div>
    </div>




</div>
