<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class FavouriteController extends Controller
{
    public function toggle($id)
    {
        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($id);

        if (!$user->isCustomer()) {
            return response()->json(['error' => 'Only customers can favourite restaurants'], 403);
        }

        $isFavourite = $user->favouriteRestaurants()->where('restaurant_id', $id)->exists();

        if ($isFavourite) {
            $user->favouriteRestaurants()->detach($id);
        } else {
            $user->favouriteRestaurants()->attach($id);
        }

        return response()->json(['favourite' => !$isFavourite]);
    }
}
