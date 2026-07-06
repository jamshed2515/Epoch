<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Professional extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'bio',
        'experience_years',
        'location',
        'photo',
        'consultation_fee',
        'session_duration',
        'is_active',
        'rating',
        'total_reviews',
        'specializations',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'specializations' => 'array',
        'consultation_fee' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    // Relationships
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function availabilities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Availability::class);
    }

    public function reviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Review::class);
    }

    // Helpers
    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        // High-resolution, professional square portraits from Unsplash
        // Uses &crop=faces to auto-detect and center the face perfectly, preventing cutoff!
        $maleAvatars = [
            'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Executive man
            'https://images.unsplash.com/photo-1537368910025-700350fe46c7?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Doctor male
            'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Businessman
            'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Tutor male
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Executive consultant
            'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Professional advisor
        ];

        $femaleAvatars = [
            'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Executive woman
            'https://images.unsplash.com/photo-1559839734-2b71ea197ec2?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Doctor female
            'https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Smiling teacher
            'https://images.unsplash.com/photo-1567532939604-b6b5b0db2604?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Therapist female
            'https://images.unsplash.com/photo-1594744803329-e58b31de215f?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Corporate manager
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&crop=faces&w=600&h=600&q=80', // Professional lady
        ];

        $name = strtolower($this->user->name ?? '');

        // Detect gender based on common title prefixes or first names
        $isFemale = false;
        $femaleKeywords = ['priya', 'sunita', 'meera', 'pooja', 'neha', 'anjali', 'rekha', 'lakshmi', 'preeti', 'nidhi', 'ms.', 'mrs.'];
        foreach ($femaleKeywords as $keyword) {
            if (str_contains($name, $keyword)) {
                $isFemale = true;
                break;
            }
        }

        // Stable selection index based on the professional's ID
        $index = ($this->id ?? 0) % 6;

        return $isFemale ? $femaleAvatars[$index] : $maleAvatars[$index];
    }

    /**
     * Get available time slots for a specific date.
     */
    public function getAvailableSlotsForDate(string $date): array
    {
        $carbon = Carbon::parse($date);
        $dayOfWeek = $carbon->dayOfWeek; // 0=Sun, 6=Sat

        $availability = $this->availabilities()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();

        if (!$availability) {
            return [];
        }

        $slots = [];
        $duration = $this->session_duration ?: 30;
        $start = Carbon::parse($availability->start_time);
        $end = Carbon::parse($availability->end_time);

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotLabel = $start->format('H:i') . '-' . $start->copy()->addMinutes($duration)->format('H:i');
            $slots[] = $slotLabel;
            $start->addMinutes($duration);
        }

        // Remove already booked slots
        $booked = $this->appointments()
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('time_slot')
            ->toArray();

        return array_values(array_diff($slots, $booked));
    }

    public function updateRating(): void
    {
        $avg = $this->reviews()->avg('rating');
        $count = $this->reviews()->count();
        $this->update(['rating' => round($avg, 2), 'total_reviews' => $count]);
    }
}
