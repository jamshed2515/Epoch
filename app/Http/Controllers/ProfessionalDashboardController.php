<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentCancelledMail;
use App\Mail\AppointmentConfirmedMail;
use App\Models\Appointment;
use App\Models\Availability;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ProfessionalDashboardController extends Controller
{
    public function index()
    {
        $professional = Auth::user()->professional;

        if (!$professional) {
            return redirect()->route('professional.profile.create')
                ->with('info', 'Please complete your professional profile first.');
        }

        $pendingAppointments = Appointment::with(['user'])
            ->where('professional_id', $professional->id)
            ->where('status', 'pending')
            ->orderBy('appointment_date')
            ->get();

        $upcomingAppointments = Appointment::with(['user'])
            ->where('professional_id', $professional->id)
            ->where('status', 'confirmed')
            ->where('appointment_date', '>=', today())
            ->orderBy('appointment_date')
            ->take(10)
            ->get();

        $stats = [
            'pending'    => Appointment::where('professional_id', $professional->id)->where('status', 'pending')->count(),
            'confirmed'  => Appointment::where('professional_id', $professional->id)->where('status', 'confirmed')->count(),
            'completed'  => Appointment::where('professional_id', $professional->id)->where('status', 'completed')->count(),
            'total'      => Appointment::where('professional_id', $professional->id)->count(),
            'revenue'    => Appointment::where('professional_id', $professional->id)
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('fee'),
        ];

        $availabilities = Availability::where('professional_id', $professional->id)->orderBy('day_of_week')->get();

        return view('dashboard.professional', compact(
            'professional', 'pendingAppointments', 'upcomingAppointments', 'stats', 'availabilities'
        ));
    }

    public function confirm(Appointment $appointment)
    {
        abort_if(Auth::user()->professional->id !== $appointment->professional_id, 403);

        $appointment->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        try {
            Mail::to($appointment->user->email)->queue(
                new AppointmentConfirmedMail($appointment)
            );
        } catch (\Exception $e) {}

        return back()->with('success', 'Appointment confirmed and user notified.');
    }

    public function reject(Request $request, Appointment $appointment)
    {
        abort_if(Auth::user()->professional->id !== $appointment->professional_id, 403);

        $request->validate(['reason' => 'nullable|string|max:255']);

        $appointment->update([
            'status'              => 'cancelled',
            'cancelled_at'        => now(),
            'cancellation_reason' => $request->reason ?? 'Rejected by professional',
        ]);

        try {
            Mail::to($appointment->user->email)->queue(
                new AppointmentCancelledMail($appointment)
            );
        } catch (\Exception $e) {}

        return back()->with('success', 'Appointment rejected.');
    }

    public function complete(Appointment $appointment)
    {
        abort_if(Auth::user()->professional->id !== $appointment->professional_id, 403);
        $appointment->update(['status' => 'completed']);
        return back()->with('success', 'Appointment marked as completed.');
    }

    public function updateAvailability(Request $request)
    {
        $professional = Auth::user()->professional;

        $request->validate([
            'availabilities'                     => ['required', 'array'],
            'availabilities.*.day_of_week'       => ['required', 'integer', 'between:0,6'],
            'availabilities.*.start_time'        => ['required', 'date_format:H:i'],
            'availabilities.*.end_time'          => ['required', 'date_format:H:i', 'after:availabilities.*.start_time'],
        ]);

        Availability::where('professional_id', $professional->id)->delete();

        foreach ($request->availabilities as $avail) {
            Availability::create([
                'professional_id' => $professional->id,
                'day_of_week'     => $avail['day_of_week'],
                'start_time'      => $avail['start_time'],
                'end_time'        => $avail['end_time'],
                'is_active'       => true,
            ]);
        }

        return back()->with('success', 'Availability updated.');
    }
}
