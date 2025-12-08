<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\RestaurantPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RestaurantPhotoController extends Controller
{
    public function store(Request $request, $restaurantId)
    {
        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($restaurantId);

        if (!$user || !$user->isOwner() || $restaurant->owner_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'photos.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:4096'
        ]);

        if ($request->hasFile('photos')) {
            $order = RestaurantPhoto::where('restaurant_id', $restaurant->id)
                ->max('display_order') ?? 0;

            foreach ($request->file('photos') as $file) {
                $path = $file->store('restaurant_photos', 'public');

                RestaurantPhoto::create([
                    'restaurant_id' => $restaurant->id,
                    'link' => $path,
                    'display_order' => ++$order,
                    'title' => null, // lub coś innego
                    'price' => null
                ]);
            }

            return back()->with('success', 'Photos uploaded successfully!');
        }

        return back()->with('error', 'No photos selected.');
    }

    public function update(Request $request, $restaurantId, $photoId)
    {
    }

    public function destroy($restaurantId, $photoId)
    {
    }
}