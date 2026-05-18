@component('mail::message')
# Appointment Confirmed! 🎉

Hi **{{ $user->name }}**,

Great news! Your appointment has been **confirmed**.

@component('mail::panel')
**Professional:** {{ $professional->user->name }}
**Date:** {{ $appointment->appointment_date->format('l, d F Y') }}
**Time:** {{ $appointment->time_slot }}
**Duration:** {{ $professional->session_duration }} minutes
**Fee:** ₹{{ number_format($appointment->fee) }}
@endcomponent

@if($appointment->notes)
**Your Notes:** {{ $appointment->notes }}
@endif

@component('mail::button', ['url' => $url, 'color' => 'primary'])
View Appointment Details
@endcomponent

Please be on time. If you need to cancel, you can do so from your appointments page at least 2 hours in advance.

Thanks,
**{{ config('app.name') }} Team**
@endcomponent
