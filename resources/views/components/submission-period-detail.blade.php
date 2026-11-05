<x-detail-wrapper>
      <livewire:tables.submission-period-form-table :wire:key="'submission-period-data-table-'.$row->id"
        :submissionPeriodRow="$row"
         :currentRoutePrefix="Route::current()->getPrefix()"
         :rowData="$options['rowData']" />
</x-detail-wrapper>
