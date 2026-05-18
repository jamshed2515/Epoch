<?php

namespace App\Policies;

use App\Models\Professional;
use App\Models\User;

class ProfessionalPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(?User $user, Professional $professional): bool
    {
        return true; // Anyone can view a professional's profile
    }

    public function create(User $user): bool
    {
        return $user->role === 'professional' && !$user->professional;
    }

    public function update(User $user, Professional $professional): bool
    {
        return $user->id === $professional->user_id || $user->isAdmin();
    }

    public function delete(User $user, Professional $professional): bool
    {
        return $user->id === $professional->user_id || $user->isAdmin();
    }
}
