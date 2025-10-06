<x-mail::message>
{!! $message !!}
<x-mail::button :url="$url">
Go to Website
</x-mail::button>

@if(\App\Models\SystemDetail::find(1))
<p style="color:#FF6600;font-weight: bold;padding-bottom: 0;margin-bottom: 0;">
   {{ config('app.name') }}
</p>
<p style="color:#404040;max-width: 400px;text-wrap: break-word; padding-bottom: 0;margin-bottom: 0;">{{ \App\Models\SystemDetail::find(1)?->address }}</p>
<p style="color:#404040;max-width: 300px;text-wrap: break-word; padding-bottom: 0;margin-bottom: 0;"><a href="{{ \App\Models\SystemDetail::find(1)?->website }}" style="color:#404040;text-decoration:underline" target="_blank">
    {{ \App\Models\SystemDetail::find(1)?->website }}
</a></p>
@else
{{ config('app.name') }}
@endif

</x-mail::message>
