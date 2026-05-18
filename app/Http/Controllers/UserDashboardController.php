<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $upcomingAppointments = Appointment::with(['professional.user', 'professional.category'])
            ->where('user_id', $user->id)
            ->upcoming()
            ->take(5)
            ->get();

        // Past = date already passed OR marked completed — scoped to this user
        $pastAppointments = Appointment::with(['professional.user', 'professional.category'])
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('appointment_date', '<', today())
                  ->orWhere('status', 'completed');
            })
            ->orderByDesc('appointment_date')
            ->take(5)
            ->get();

        $stats = [
            'total'     => Appointment::where('user_id', $user->id)->count(),
            'upcoming'  => Appointment::where('user_id', $user->id)->upcoming()->count(),
            'completed' => Appointment::where('user_id', $user->id)->where('status', 'completed')->count(),
            'cancelled' => Appointment::where('user_id', $user->id)->where('status', 'cancelled')->count(),
        ];

        return view('dashboard.user', compact('upcomingAppointments', 'pastAppointments', 'stats'));
    }
}
