<?php

namespace App\Policies;

use App\Models\Reply;
use App\Models\Review;
use App\Models\User;

class ReplyPolicy
{
    /**
     * Determine if user can create a reply to a review.
     * Only the owner of the restaurant being reviewed.
     */
    public function create(User $user, Review $review): bool
    {
        return $user->isOwner() && $review->restaurant->owner_id === $user->id;
    }

    /**
     * Determine if user can view the reply.
     * Anyone can view replies.
     */
    public function view(?User $user, Reply $reply): bool
    {
        return true;
    }

    /**
     * Determine if user can update the reply.
     * Only the author of the reply.
     */
    public function update(User $user, Reply $reply): bool
    {
        return $user->id === $reply->user_id;
    }

    /**
     * Determine if user can delete the reply.
     * Only the author of the reply.
     */
    public function delete(User $user, Reply $reply): bool
    {
        return $user->id === $reply->user_id;
    }
}
