<div class="alert alert-danger " role="alert" x-ref="errorAlert" x-data x-init="() => {

    let object = $($refs.errorAlert);
    object.fadeTo(30000, 0).slideUp(500);
}">
    <strong>Error!</strong>
    {{ $slot }}
</div>
