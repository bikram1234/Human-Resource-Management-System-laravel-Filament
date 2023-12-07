@component('mail::message')

# No Due Application

Dear {{ $approval }},

{{ $currentUser->name }} has applied for the No Due Clearance.

Please visit @component('mail::button', ['url' => route('login')])

Here

@endcomponent

to review the details and take necessary action.

Thank you for using our application system.

Regards,

{{ config('app.name') }}

@endcomponent
