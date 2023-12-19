@component('mail::message')
# Leave Encashment Application

Dear {{ $approval->name }},

{{$currentUser->name}} have applied for the Leave Encashment.
Please visit @component('mail::button', ['url' => route('login')])
Here
@endcomponent
to review the details and take necessary action.

Thank you for using our application system.

Regards,
{{ config('app.name') }}
@endcomponent