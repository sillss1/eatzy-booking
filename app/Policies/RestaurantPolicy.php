<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Restaurant;

class RestaurantPolicy


{
   
    public function view(?User $user, Restaurant $restaurant): bool
    {
        
        return true;
    }

 
    public function create(User $user): bool
    {
        
        return $user->isOwner() || $user->isAdmin();
    }

    public function update(User $user, Restaurant $restaurant): bool
    {
       
        return $user->isAdmin() || $user->id === $restaurant->owner_id;
    }

    public function delete(User $user, Restaurant $restaurant): bool
    {
       
        return $user->isAdmin() || $user->id === $restaurant->owner_id;
    }
}
