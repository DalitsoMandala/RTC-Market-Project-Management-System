<div>
    <style>
        .iti {
            width: 100%;
            display: block;
        }
    </style>
    <input wire:ignore x-data="{ value: @entangle($attributes->wire('model')) }" x-ref="input" x-init="const iti = window.intlTelInput($refs.input, {
        initialCountry: 'auto',
        nationalMode: false,
        strictMode: true,
        separateDialCode: true,
        geoIpLookup: function(callback) {
            $.get('https://ipinfo.io?token=63820e88ff3812',
                function() {}, 'jsonp').always(function(resp) {
                var countryCode = (resp && resp.country) ? resp.country : 'MW';
    
                callback(countryCode);
            });
        },
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.12/build/js/utils.js',
    })" x-on:
        change="value=$event.target.value" type="tel" {!! $attributes->merge([
            'class' => 'form-control',
        ]) !!} />
</div>
