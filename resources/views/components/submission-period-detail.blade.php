<x-detail-wrapper>

      <livewire:tables.submission-period-form-table :wire:key="'submission-period-data-table-'.$row->id"
        :submissionPeriodRow="$row->id"
         :currentRoutePrefix="Route::current()->getPrefix()" />
</x-detail-wrapper>
