<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Determine if user can create a review.
     * Only customers can create reviews.
     */
    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    /**
     * Determine if user can view the review.
     * Anyone can view reviews.
     */
    public function view(?User $user, Review $review): bool
    {
        return true;
    }

    /**
     * Determine if user can update the review.
     * Only the author of the review.
     */
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    /**
     * Determine if user can delete the review.
     * Only the author of the review.
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }
}
