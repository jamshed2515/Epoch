<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use Illuminate\Http\Request;

class ProfessionalApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Professional::with(['user:id,name', 'category:id,name,slug'])
            ->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }
        if ($request->filled('search')) {
            $query->whereHas('user', fn($q) => $q->where('name', 'like', '%'.$request->search.'%'));
        }

        $professionals = $query->orderByDesc('rating')->paginate(15);

        return response()->json(['status' => 'success', 'data' => $professionals]);
    }

    public function show(Professional $professional)
    {
        $professional->load(['user:id,name,email', 'category', 'availabilities', 'reviews.user:id,name']);

        $availableDates = [];
        for ($i = 1; $i <= 7; $i++) {
            $date = now()->addDays($i)->format('Y-m-d');
            $slots = $professional->getAvailableSlotsForDate($date);
            if (!empty($slots)) {
                $availableDates[$date] = $slots;
            }
        }

        return response()->json([
            'status' => 'success',
            'data'   => $professional,
            'slots'  => $availableDates,
        ]);
    }
}
