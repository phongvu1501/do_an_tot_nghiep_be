<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Auth\Access\Response;

class ReviewPolicy
{
    public function view(User $user, Reservation $reservation)
    {
        return $user->id === $reservation->user_id;
    }

    public function update(User $user, Review $review)
    {
        return $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review)
    {
        return $user->id === $review->user_id;
    }
}
