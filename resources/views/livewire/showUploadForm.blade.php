<div x-data x-init="$wire.on('showModal', (e) => {

    const myModal = new bootstrap.Modal(document.getElementById(e.name), {})
    myModal.show();
})">


    <x-modal id="view-upload-modal" title="Upload form">

    </x-modal>

</div>
