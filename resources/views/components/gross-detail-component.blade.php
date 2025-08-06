<div class="row bg-soft-secondary">


        <div class="col-5 ">


            <div wire:ignore>
                @php

                    $route = Route::current()->getPrefix();
                @endphp
            </div>
            <div class="" >


                <livewire:tables.gross-margin-item-table :row="$row" :wire:key="'gmi-table-'.$row->id"/>
            </div>

        </div>




</div>
