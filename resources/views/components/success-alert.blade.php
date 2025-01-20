<div class="alert alert-success" role="alert" x-ref="successAlert" x-data x-init="() => {

    let object = $($refs.successAlert);
    object.fadeTo(10000, 0).slideUp(500);
}">
    <strong>Success!</strong>
    {{ $slot }}
</div>
