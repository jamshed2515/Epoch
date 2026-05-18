<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentApiController extends Controller
{
    public function index(Request $request)
    {
        $appointments = Appointment::with(['user:id,name,email', 'professional.user:id,name'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data'   => $appointments,
        ]);
    }

    public function store(AppointmentRequest $request)
    {
        $professional = Professional::findOrFail($request->professional_id);

        $appointment = Appointment::create([
            'user_id'          => Auth::id(),
            'professional_id'  => $request->professional_id,
            'appointment_date' => $request->appointment_date,
            'time_slot'        => $request->time_slot,
            'notes'            => $request->notes,
            'fee'              => $professional->consultation_fee,
            'status'           => 'pending',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment booked successfully.',
            'data'    => $appointment->load('professional.user'),
        ], 201);
    }

    public function show(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id()
            && Auth::user()->professional?->id !== $appointment->professional_id
            && !Auth::user()->isAdmin()
        ) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $appointment->load(['user:id,name,email', 'professional.user:id,name']),
        ]);
    }

    public function update(Request $request, Appointment $appointment)
    {
        $professional = Auth::user()->professional;

        if (!$professional || $professional->id !== $appointment->professional_id) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:confirmed,cancelled,completed',
            'reason' => 'nullable|string|max:255',
        ]);

        $appointment->update([
            'status'              => $request->status,
            'cancellation_reason' => $request->reason,
            'confirmed_at'        => $request->status === 'confirmed' ? now() : $appointment->confirmed_at,
            'cancelled_at'        => $request->status === 'cancelled' ? now() : $appointment->cancelled_at,
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Appointment updated.',
            'data'    => $appointment->fresh(),
        ]);
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        if (!$appointment->canBeCancelledByUser()) {
            return response()->json(['status' => 'error', 'message' => 'Appointment cannot be cancelled.'], 422);
        }

        $appointment->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        return response()->json(['status' => 'success', 'message' => 'Appointment cancelled.']);
    }
}
