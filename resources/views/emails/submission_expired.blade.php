<x-mail::message>
# Hello {{ $user->name }},

<p>This is a  reminder that your submission period is {{ strtolower($reminderType) }}.</p>

<p><strong>Submission Period Details:</strong></p>
<ul>
 <li><strong>Start Date:</strong>
{{ \Carbon\Carbon::parse($submissionPeriod['date_established'])->format('d-m-Y H:i:A') }}</li>
 <li><strong>End Date:</strong>
{{ \Carbon\Carbon::parse($submissionPeriod['date_ending'])->format('d-m-Y H:i:A') }}</li>
</ul>


<p>If you have any questions or need assistance, feel free to contact our M&E team.</p>
 <p>Best regards,<br>
@php
$route = route('login');
@endphp
<x-mail::button :url="$route">
Go to Website
</x-mail::button>
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
