@component('mail::message')
# Appointment Cancelled

Hi **{{ $user->name }}**,

We're sorry to inform you that your appointment has been **cancelled**.

@component('mail::panel')
**Professional:** {{ $professional->user->name }}
**Date:** {{ $appointment->appointment_date->format('l, d F Y') }}
**Time:** {{ $appointment->time_slot }}
@if($appointment->cancellation_reason)
**Reason:** {{ $appointment->cancellation_reason }}
@endif
@endcomponent

You can book a new appointment at any time.

@component('mail::button', ['url' => $url, 'color' => 'error'])
Book a New Appointment
@endcomponent

We apologize for any inconvenience caused.

Thanks,
**{{ config('app.name') }} Team**
@endcomponent
