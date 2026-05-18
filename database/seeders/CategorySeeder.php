<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Doctors',
                'slug'        => 'doctors',
                'description' => 'Book appointments with licensed medical doctors across various specializations.',
                'icon'        => 'stethoscope',
                'color'       => '#ef4444',
            ],
            [
                'name'        => 'Tutors',
                'slug'        => 'tutors',
                'description' => 'Find expert tutors for academic subjects and skill development.',
                'icon'        => 'book-open',
                'color'       => '#3b82f6',
            ],
            [
                'name'        => 'Consultants',
                'slug'        => 'consultants',
                'description' => 'Business, financial, and strategy consultants for your needs.',
                'icon'        => 'briefcase',
                'color'       => '#8b5cf6',
            ],
            [
                'name'        => 'Lawyers',
                'slug'        => 'lawyers',
                'description' => 'Legal advice and representation from qualified attorneys.',
                'icon'        => 'scale',
                'color'       => '#f59e0b',
            ],
            [
                'name'        => 'Therapists',
                'slug'        => 'therapists',
                'description' => 'Mental health professionals offering counseling and therapy.',
                'icon'        => 'heart',
                'color'       => '#ec4899',
            ],
            [
                'name'        => 'Fitness Coaches',
                'slug'        => 'fitness-coaches',
                'description' => 'Personal trainers and fitness coaches for your health goals.',
                'icon'        => 'activity',
                'color'       => '#10b981',
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
