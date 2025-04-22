<div x-ref="warningAlert" x-data x-init="() => {

    let object = $($refs.warningAlert);
    object.fadeTo(30000, 0).slideUp(500);
}" class="alert alert-warning" role="alert">
    {{ $slot }}
</div>
