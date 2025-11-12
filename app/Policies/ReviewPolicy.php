<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    public function view(User $user, Reservation $reservation): bool
    {
        // User là người tạo reservation hoặc role = admin mới được xem
        return $user->id === $reservation->user_id || $user->role === 'admin';
    }

    public function store(User $user, Reservation $reservation): bool
    {
        return $user->id === $reservation->user_id && $reservation->status === 'completed';
    }

    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
}
