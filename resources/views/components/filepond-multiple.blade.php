<div wire:ignore x-data="{}" x-init="const inputElement = $refs.input;
pond = FilePond.create($refs.input, {
    server: {
        process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {

            setTimeout(() => {
                @this.upload('{{ $attributes['wire:model'] }}', file, load, error, progress)
            }, 2000)


        },
        revert: (filename, load) => {


            @this.removeUpload('{{ $attributes['wire:model'] }}', filename, load)


        },
    },
    acceptedFileTypes: ['application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf', 'image/jpeg', 'image/png'],
    labelFileTypeNotAllowed: 'Only supported files  are allowed.',
    fileValidateTypeLabelExpectedTypes: 'Expects (.xlsx,.docx,.pdf,.jpg,.jpeg,.png)',
    labelInvalidField: 'Invalid file',
    allowImagePreview: false,
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
    <input type="file" multiple class="form-control" x-ref="input" wire:loading.attr='disabled' />
</div>
