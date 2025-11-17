<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Restaurant;

class RestaurantPolicy

//we need to add  verifications to the user model 
{
   
    public function view(?User $user, Restaurant $restaurant): bool
    {
        
        return true;
    }

 
    public function create(User $user): bool
    {
        // Only owners or admins can create restaurants
        return $user->isOwner() || $user->isAdmin();
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
        // Only the owner or an admin can update
        return $user->isAdmin() || $user->id === $restaurant->owner_id;
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
        // Only the owner or an admin can delete
        return $user->isAdmin() || $user->id === $restaurant->owner_id;
    }
}
