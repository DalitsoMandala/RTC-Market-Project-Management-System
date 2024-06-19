<div wire:ignore x-data="{}" x-init="const inputElement = $refs.input;
pond = FilePond.create($refs.input, {
    server: {
        process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {


            @this.upload('{{ $attributes['wire:model'] }}', file, load, error, progress)

        },
        revert: (filename, load) => {
            @this.removeUpload('{{ $attributes['wire:model'] }}', filename, load)


        },
    },
    acceptedFileTypes: ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
    labelFileTypeNotAllowed: 'Only Excel files  are allowed.',
    fileValidateTypeLabelExpectedTypes: 'Expects (.xlsx)',
    labelInvalidField: 'Invalid file',
    credits: false,
    maxFileSize: '5MB',
    allowRevert: true,
    //  instantUpload: false,
    forceRevert: true,
    // allowProcess: true,
    allowRemove: true,
    onerror: (file, error) => {
        $wire.dispatch('remove-errors');
    },
    instantUpload: {{ $attributes['instantUpload'] }}
});

$wire.on('removeUploadedFile', function() {

    myTimeout = setTimeout(() => {
        pond.removeFiles({ revert: true });
    }, 5000);


});

$wire.on('errorRemove', function() {


    pond.removeFiles({ revert: true });


});

// Listen for upload progress event
pond.on('addfile', (error, file) => {

    $wire.dispatch('uploading-files');
});




pond.on('processfiles', () => {
    $wire.dispatch('finished-uploading');

});">


    <!-- An unexamined life is not worth living. - Socrates -->
    <input type="file" class="form-control" x-ref="input" />
</div>
