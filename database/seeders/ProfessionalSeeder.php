<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Category;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ProfessionalSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::all();
        if ($categories->isEmpty()) {
            return;
        }

        $names = [
            'Dr. Arjun Nair', 'Dr. Pooja Pillai', 'Prof. Sameer Khan', 'Adv. Neha Desai',
            'Mr. Kiran Rao', 'Dr. Anjali Singh', 'Dr. Vivek Joshi', 'Ms. Rekha Thomas',
            'Prof. Suresh Iyer', 'Dr. Lakshmi Reddy', 'Mr. Aditya Bose', 'Adv. Preeti Menon',
            'Dr. Rajesh Gupta', 'Prof. Nidhi Shah', 'Mr. Harish Verma',
        ];

        foreach ($names as $i => $name) {
            $email = strtolower(str_replace([' ', '.'], ['_', ''], $name)) . $i . '@appointease.com';
            $category = $categories->random();

            $user = User::firstOrCreate(['email' => $email], [
                'name'     => $name,
                'password' => Hash::make('password'),
                'role'     => 'professional',
            ]);

            $professional = Professional::firstOrCreate(['user_id' => $user->id], [
                'category_id'      => $category->id,
                'bio'              => "Experienced professional with over " . rand(3, 20) . " years in the field. "
                    . "Committed to providing high-quality, personalized service to every client. "
                    . "Specializes in comprehensive consultations and tailored solutions.",
                'experience_years' => rand(3, 20),
                'location'         => collect(['Mumbai', 'Delhi', 'Bangalore', 'Chennai', 'Hyderabad', 'Pune', 'Kolkata'])->random() . ', India',
                'consultation_fee' => collect([300, 500, 600, 750, 800, 1000, 1200, 1500])->random(),
                'session_duration' => collect([30, 45, 60])->random(),
                'is_active'        => true,
                'rating'           => round(rand(35, 50) / 10, 1),
                'total_reviews'    => rand(5, 120),
                'specializations'  => collect(['Consultation', 'Follow-up Care', 'Preventive', 'Diagnosis', 'Advisory'])->random(rand(2, 3))->values()->toArray(),
            ]);

            // Availability: Mon-Fri
            for ($day = 1; $day <= 5; $day++) {
                Availability::firstOrCreate([
                    'professional_id' => $professional->id,
                    'day_of_week'     => $day,
                ], [
                    'start_time' => collect(['08:00', '09:00', '10:00'])->random(),
                    'end_time'   => collect(['16:00', '17:00', '18:00'])->random(),
                    'is_active'  => true,
                ]);
            }
        }
    }
}
