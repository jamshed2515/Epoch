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
        $name = urlencode($this->user->name ?? 'Pro');
        return "https://ui-avatars.com/api/?name={$name}&background=6366f1&color=fff&size=256";
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
