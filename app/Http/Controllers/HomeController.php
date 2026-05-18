<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Professional;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('professionals')->get();

        $featuredProfessionals = Professional::with(['user', 'category'])
            ->where('is_active', true)
            ->orderByDesc('rating')
            ->orderByDesc('total_reviews')
            ->take(8)
            ->get();

        $stats = [
            'professionals' => Professional::where('is_active', true)->count(),
            'categories'    => Category::count(),
            'appointments'  => \App\Models\Appointment::count(),
            'users'         => \App\Models\User::where('role', 'user')->count(),
        ];

        return view('home.index', compact('categories', 'featuredProfessionals', 'stats'));
    }
}
