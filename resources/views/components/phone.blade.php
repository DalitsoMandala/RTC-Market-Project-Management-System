<div>
    <style>
        .iti {
            width: 100%;
            display: block;
        }
    </style>
    <input wire:ignore x-data="{ value: @entangle($attributes->wire('model')) }" x-ref="input" x-init="window.intlTelInput($refs.input, {
        initialCountry: 'auto',
        nationalMode: false,

        geoIpLookup: function(callback) {
            $.get('https://ipinfo.io?token=63820e88ff3812',
                function() {}, 'jsonp').always(function(resp) {
                var countryCode = (resp && resp.country) ? resp.country : 'mw';
                console.log(countryCode);
                callback(countryCode);
            });
        },


    })"
        x-on:change="value = $event.target.value" type="tel" {!! $attributes->merge([
            'class' => 'form-control',
        ]) !!} />
</div>
