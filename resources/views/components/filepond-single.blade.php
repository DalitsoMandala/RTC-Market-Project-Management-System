<div {{ $attributes->except(['wire:model', 'instantUpload'])->merge(['class' => '']) }} wire:ignore x-data
    x-init="const inputElement = $refs.input;
    
    const pond = FilePond.create(inputElement, {
        server: {
            process: (
                fieldName,
                file,
                metadata,
                load,
                error,
                progress,
                abort,
                transfer,
                options
            ) => {
    
                $wire.upload(
                    '{{ $attributes['wire:model'] }}',
                    file,
                    load,
                    error,
                    progress
                );
    
                return {
                    abort: () => {
                        abort();
                    }
                };
            },
    
            revert: (filename, load, error) => {
    
                $wire.removeUpload(
                    '{{ $attributes['wire:model'] }}',
                    filename,
                    load,
                    error
                );
            }
        },
    
        acceptedFileTypes: [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ],
    
        labelFileTypeNotAllowed: 'Only Excel files are allowed.',
    
        fileValidateTypeLabelExpectedTypes: 'Expects (.xlsx)',
    
        labelInvalidField: 'Invalid file',
    
        credits: false,
    
        maxFileSize: '5MB',
    
        allowRevert: true,
    
        forceRevert: true,
    
        allowRemove: true,
    
        instantUpload: {{ $attributes['instantUpload'] ?? 'true' }},
    
        onerror: (file, error) => {
            $wire.dispatch('remove-errors');
        }
    });
    
    
    // File added
    pond.on('addfile', (error, file) => {
    
        if (error) {
            return;
        }
    
        $wire.dispatch('uploading-files');
    });
    
    
    // Upload finished
    pond.on('processfile', (error, file) => {
    
        if (error) {
            return;
        }
    
        $wire.dispatch('finished-uploading');
    });
    
    
    // Remove the FilePond file when Livewire tells us to
    $wire.on('removeUploadedFile', () => {
    
        pond.removeFiles({
            revert: true
        });
    
    });
    
    
    // Optional error event
    $wire.on('errorRemove', () => {
    
        // Do not remove the FilePond file here.
        // This prevents Livewire updates from accidentally
        // clearing the selected file.
    
    });
    
    
    // Store pond instance for debugging if necessary
    window.filePondInstance = pond;">
    <input type="file" x-ref="input" class="form-control">
</div>
