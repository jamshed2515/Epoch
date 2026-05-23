<?php

namespace Database\Seeders;

use App\Models\Availability;
use App\Models\Category;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        $admin = User::firstOrCreate(['email' => 'admin@epoch.com'], [
            'name'     => 'Admin User',
            'password' => Hash::make('password'),
            'role'     => 'admin',
            'phone'    => '+91-9000000001',
        ]);

        $categories = Category::all();

        // 2. Professionals
        $professionalsData = [
            ['name' => 'Dr. Priya Sharma', 'email' => 'priya@epoch.com', 'category' => 'doctors',
             'bio' => 'Experienced cardiologist with over 15 years in clinical practice. Specializes in heart disease prevention and management.',
             'experience_years' => 15, 'location' => 'Mumbai, Maharashtra', 'consultation_fee' => 800, 'specializations' => ['Cardiology', 'Hypertension', 'Preventive Care']],

            ['name' => 'Prof. Rahul Mehta', 'email' => 'rahul@epoch.com', 'category' => 'tutors',
             'bio' => 'IIT alumnus offering expert tutoring in Mathematics, Physics, and JEE/NEET preparation.',
             'experience_years' => 8, 'location' => 'Delhi', 'consultation_fee' => 500, 'specializations' => ['Mathematics', 'Physics', 'JEE Prep']],

            ['name' => 'Adv. Sunita Kapoor', 'email' => 'sunita@epoch.com', 'category' => 'lawyers',
             'bio' => 'Senior advocate with expertise in civil law, family law, and corporate matters.',
             'experience_years' => 12, 'location' => 'Bangalore', 'consultation_fee' => 1000, 'specializations' => ['Civil Law', 'Family Law', 'Corporate']],

            ['name' => 'Mr. Arun Verma', 'email' => 'arun@epoch.com', 'category' => 'consultants',
             'bio' => 'Business strategy consultant helping SMEs scale their operations and improve profitability.',
             'experience_years' => 10, 'location' => 'Hyderabad', 'consultation_fee' => 1500, 'specializations' => ['Strategy', 'Finance', 'Operations']],

            ['name' => 'Dr. Meera Nair', 'email' => 'meera@epoch.com', 'category' => 'therapists',
             'bio' => 'Clinical psychologist specializing in anxiety, depression, and cognitive behavioral therapy.',
             'experience_years' => 7, 'location' => 'Chennai', 'consultation_fee' => 600, 'specializations' => ['CBT', 'Anxiety', 'Depression']],
        ];

        foreach ($professionalsData as $data) {
            $category = $categories->firstWhere('slug', $data['category']);
            if (!$category) continue;

            $user = User::firstOrCreate(['email' => $data['email']], [
                'name'     => $data['name'],
                'password' => Hash::make('password'),
                'role'     => 'professional',
                'phone'    => '+91-' . rand(9000000000, 9999999999),
            ]);

            $professional = Professional::firstOrCreate(['user_id' => $user->id], [
                'category_id'      => $category->id,
                'bio'              => $data['bio'],
                'experience_years' => $data['experience_years'],
                'location'         => $data['location'],
                'consultation_fee' => $data['consultation_fee'],
                'session_duration' => 30,
                'is_active'        => true,
                'specializations'  => $data['specializations'],
                'rating'           => round(rand(38, 50) / 10, 1),
                'total_reviews'    => rand(10, 80),
            ]);

            // Create Mon-Sat availability
            for ($day = 1; $day <= 6; $day++) {
                Availability::firstOrCreate([
                    'professional_id' => $professional->id,
                    'day_of_week'     => $day,
                ], [
                    'start_time' => '09:00',
                    'end_time'   => '17:00',
                    'is_active'  => true,
                ]);
            }
        }

        // 3. Regular users
        $usersData = [
            ['name' => 'Ananya Gupta',   'email' => 'ananya@example.com'],
            ['name' => 'Rohit Patel',    'email' => 'rohit@example.com'],
            ['name' => 'Kavya Reddy',    'email' => 'kavya@example.com'],
            ['name' => 'Vikram Singh',   'email' => 'vikram@example.com'],
            ['name' => 'Sonal Joshi',    'email' => 'sonal@example.com'],
            ['name' => 'Arjun Kumar',    'email' => 'arjun@example.com'],
            ['name' => 'Divya Sharma',   'email' => 'divya@example.com'],
            ['name' => 'Manish Tiwari',  'email' => 'manish@example.com'],
            ['name' => 'Pooja Mishra',   'email' => 'pooja@example.com'],
            ['name' => 'Varun Chopra',   'email' => 'varun@example.com'],
        ];

        foreach ($usersData as $userData) {
            User::firstOrCreate(['email' => $userData['email']], [
                'name'     => $userData['name'],
                'password' => Hash::make('password'),
                'role'     => 'user',
                'phone'    => '+91-' . rand(8000000000, 8999999999),
            ]);
        }
    }
}
