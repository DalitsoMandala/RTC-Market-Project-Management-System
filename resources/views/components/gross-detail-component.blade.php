<div class="row bg-soft-secondary">

    <div class="m-2 card ">
            <div class="card-body">
        <!-- Nav Tabs -->
        <ul class="my-2 nav nav-tabs" id="gm-table-tabs-{{ $row->id }}" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="items-tab-{{ $row->id }}" data-bs-toggle="tab"
                    data-bs-target="#items-{{ $row->id }}" type="button" role="tab"
                    aria-controls="items-{{ $row->id }}" aria-selected="true">
                    Items
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="varieties-tab-{{ $row->id }}" data-bs-toggle="tab"
                    data-bs-target="#varieties-{{ $row->id }}" type="button" role="tab"
                    aria-controls="varieties-{{ $row->id }}" aria-selected="false">
                    Varieties
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="mt-2 tab-content" id="gm-table-tabs-content-{{ $row->id }}">

            <!-- Items Tab -->
            <div class="tab-pane fade show active" id="items-{{ $row->id }}" role="tabpanel"
                aria-labelledby="items-tab-{{ $row->id }}">
                <div class="col-4">
                    <livewire:tables.gross-margin-item-table :row="$row" :wire:key="'gmi-table-'.$row->id" />
                </div>

            </div>

            <!-- Varieties Tab -->
            <div class="tab-pane fade" id="varieties-{{ $row->id }}" role="tabpanel"
                aria-labelledby="varieties-tab-{{ $row->id }}">
                      <div class="col-3">
                <livewire:tables.gross-margin-variety-table :row="$row" :wire:key="'gmv-table-'.$row->id" />
                      </div>
            </div>

        </div>
            </div>
    </div>

</div>
