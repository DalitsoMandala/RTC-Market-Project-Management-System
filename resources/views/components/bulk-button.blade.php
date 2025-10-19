@props(['tableName'])
<div wire:loading.class='opacity-50 pe-none'>

<span x-text='console.log(window.pgBulkActions)' ></span>
 <button type="button" @click="$dispatch('bulkDownload.'+'{{ $tableName }}')" class="btn btn-warning btn-sm" x-data x-show="window.pgBulkActions.count('{{ $tableName }}') > 0" x-text="'Download files (' + window.pgBulkActions.count('{{ $tableName }}') + ')' ">Download File(s)</button>
<button type="button" x-show="window.pgBulkActions.count('{{ $tableName }}') > 0" class="btn btn-secondary btn-sm" @click =" $dispatch('clearChecks.'+'{{ $tableName }}')" >Clear Selected</button>
</div>
