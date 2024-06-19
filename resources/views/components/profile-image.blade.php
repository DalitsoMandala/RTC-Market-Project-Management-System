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
    acceptedFileTypes: ['image/jpeg', 'image/png'],
    labelFileTypeNotAllowed: 'Only Image files  are allowed.',
    fileValidateTypeLabelExpectedTypes: 'Expects (.jpg,.jpeg)',
    labelInvalidField: 'Invalid file',
    credits: false,
    maxFileSize: '2MB',
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
