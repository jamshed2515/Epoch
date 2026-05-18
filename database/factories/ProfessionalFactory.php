<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Professional;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfessionalFactory extends Factory
{
    protected $model = Professional::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory()->create(['role' => 'professional'])->id,
            'category_id'      => Category::inRandomOrder()->first()?->id ?? Category::factory()->create()->id,
            'bio'              => fake()->paragraph(3),
            'experience_years' => fake()->numberBetween(1, 20),
            'location'         => fake()->city() . ', ' . fake()->state(),
            'consultation_fee' => fake()->randomElement([300, 500, 750, 1000, 1200, 1500, 2000]),
            'session_duration' => fake()->randomElement([30, 45, 60]),
            'is_active'        => true,
            'rating'           => round(fake()->numberBetween(30, 50) / 10, 1),
            'total_reviews'    => fake()->numberBetween(0, 100),
            'specializations'  => fake()->randomElements(
                ['Consultation', 'Follow-up', 'Emergency', 'Prevention', 'Diagnosis'],
                fake()->numberBetween(2, 4)
            ),
        ];
    }
}
