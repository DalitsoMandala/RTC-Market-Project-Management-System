<x-mail::message>
@if(!empty($greeting))
# {{ $greeting }}
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
{{ $line }}
@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    $color = match ($level ?? 'primary') {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<x-mail::button :url="$actionUrl" :color="$color">
{{ $actionText }}
</x-mail::button>
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
{{ $line }}

@endforeach

{{-- Salutation --}}
@if (!empty($salutation))
{{ $salutation }}
@else
@lang('Regards'),<br>

@if(\App\Models\SystemDetail::find(1))
<p style="color:#FF6600;font-weight: bold;padding-bottom: 0;margin-bottom: 0;">
    <span style="color: #404040">{{ config('app.name') }} | </span>{{ config('mail.from.username') }}
</p>
<p style="color:#404040;max-width: 400px;text-wrap: break-word; padding-bottom: 0;margin-bottom: 0;">{{ \App\Models\SystemDetail::find(1)?->address }}</p>
<p style="color:#404040;max-width: 300px;text-wrap: break-word; padding-bottom: 0;margin-bottom: 0;"><a href="{{ \App\Models\SystemDetail::find(1)?->website }}" style="color:#404040;text-decoration:underline" target="_blank">
    {{ \App\Models\SystemDetail::find(1)?->website }}
</a></p>
@else
{{ config('app.name') }}
@endif
@endif

{{-- Subcopy --}}
@isset($actionText)
<x-slot:subcopy>
@lang(
    "If you're having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser:',
    [
        'actionText' => $actionText,
    ]
) <span class="break-all">[{{ $displayableActionUrl }}]({{ $actionUrl }})</span>
</x-slot:subcopy>
@endisset
</x-mail::message>
