<div wire:ignore x-data="{}" x-init="const inputElement = $refs.input;
pond = FilePond.create($refs.input, {
    server: {
        process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {


            @this.upload('{{ $attributes['wire:model'] }}', file, load, error, progress)

        },
        revert: (filename, load) => {
            //  @this.removeUpload('uploadedFile', filename, load)


        },
    },
    acceptedFileTypes: ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    labelFileTypeNotAllowed: 'Only Excel files (.xls, .xlsx) are allowed.',
    credits: false,
    maxFileSize: '5MB',
    allowRevert: false,
    //  instantUpload: false,
    allowProcess: false,
    allowRemove: false,
    onerror: (file, error) => {
        $wire.dispatch('remove-errors');
    }
});

$wire.on('removeUploadedFile', function() {

    myTimeout = setTimeout(() => {
        pond.removeFiles({ revert: true });
    }, 1000);


});

// Listen for upload progress event
pond.on('addfile', (error, file) => {

    $wire.dispatch('uploading-files');
});

pond.on('processfile', () => {
    // Check if all files have been uploaded
    if (pond.getFiles().length === 0) {
        // Re-enable the ability to interact with files
        pond.setOptions({ allowProcess: true });

    }
});">


    <!-- An unexamined life is not worth living. - Socrates -->
    <input type="file" class="form-control" x-ref="input" />
</div>
