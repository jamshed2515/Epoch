<?php

namespace Database\Factories;

use App\Models\Appointment;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AppointmentFactory extends Factory
{
    protected $model = Appointment::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('-30 days', '+30 days')->format('Y-m-d');
        $hour = fake()->numberBetween(9, 16);
        $minute = fake()->randomElement(['00', '30']);
        $endHour = $minute === '30' ? $hour + 1 : $hour;
        $endMinute = $minute === '30' ? '00' : '30';
        $timeSlot = sprintf('%02d:%s-%02d:%s', $hour, $minute, $endHour, $endMinute);

        return [
            'user_id'          => User::where('role', 'user')->inRandomOrder()->first()?->id,
            'professional_id'  => Professional::inRandomOrder()->first()?->id,
            'appointment_date' => $date,
            'time_slot'        => $timeSlot,
            'status'           => fake()->randomElement(['pending', 'confirmed', 'cancelled', 'completed']),
            'notes'            => fake()->optional()->sentence(),
            'fee'              => fake()->randomElement([300, 500, 750, 1000]),
        ];
    }
}
