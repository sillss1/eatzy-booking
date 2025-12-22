<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    /**
     * Determine if user can create a restaurant.
     * Only owners can create restaurants.
     */
    public function create(User $user): bool
    {
        return $user->isOwner();
    }

    /**
     * Determine if user can view the restaurant.
     * Anyone can view active restaurants.
     */
    public function view(?User $user, Restaurant $restaurant): bool
    {
        return $restaurant->closed_at === null;
    }

    /**
     * Determine if user can update the restaurant.
     * Only the owner of this specific restaurant.
     */
    public function update(User $user, Restaurant $restaurant): bool
    {
        return $user->isOwner() && $user->id === $restaurant->owner_id;
    }

    /**
     * Determine if user can delete (close) the restaurant.
     * Only the owner of this specific restaurant.
     */
    public function delete(User $user, Restaurant $restaurant): bool
    {
        return $user->isOwner() && $user->id === $restaurant->owner_id;
    }
}
