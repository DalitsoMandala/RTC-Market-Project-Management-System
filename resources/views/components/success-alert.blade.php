<div class="alert alert-success alert-border-left" role="alert" x-ref="successAlert" x-data x-init="() => {

    let object = $($refs.successAlert);
    object.fadeTo(30000, 0).slideUp(500);
}">
    <strong>Success!</strong>
    {{ $slot }}
</div>
