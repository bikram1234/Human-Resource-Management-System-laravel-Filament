@component('mail::message')
# Leave Application

Dear {{ $user->name }},

{{ $content }}

Thank you for using our application system.

Regards,
{{ config('app.name') }}
@endcomponent