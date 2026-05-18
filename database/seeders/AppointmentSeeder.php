<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->get();
        $professionals = Professional::all();

        if ($users->isEmpty() || $professionals->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        $timeSlots = ['09:00-09:30', '09:30-10:00', '10:00-10:30', '10:30-11:00', '11:00-11:30', '11:30-12:00',
                      '13:00-13:30', '13:30-14:00', '14:00-14:30', '14:30-15:00', '15:00-15:30', '15:30-16:00'];

        $count = 0;
        $created = [];

        while ($count < 30) {
            $user = $users->random();
            $professional = $professionals->random();
            $date = now()->subDays(rand(0, 30))->addDays(rand(0, 30))->format('Y-m-d');
            $slot = $timeSlots[array_rand($timeSlots)];
            $key = "{$professional->id}_{$date}_{$slot}";

            if (isset($created[$key])) continue;
            $created[$key] = true;

            $status = $statuses[array_rand($statuses)];

            Appointment::create([
                'user_id'             => $user->id,
                'professional_id'     => $professional->id,
                'appointment_date'    => $date,
                'time_slot'           => $slot,
                'status'              => $status,
                'fee'                 => $professional->consultation_fee,
                'notes'               => rand(0, 1) ? 'Please prepare relevant medical documents.' : null,
                'confirmed_at'        => in_array($status, ['confirmed', 'completed']) ? now() : null,
                'cancelled_at'        => $status === 'cancelled' ? now() : null,
                'cancellation_reason' => $status === 'cancelled' ? 'Schedule conflict.' : null,
            ]);

            $count++;
        }
    }
}
