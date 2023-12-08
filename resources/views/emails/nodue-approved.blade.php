@component('mail::message')
# Leave Application

Dear {{ $user }},

{{ $content }}

Thank you for using our application system.

Regards,
{{ config('app.name') }}
@endcomponent