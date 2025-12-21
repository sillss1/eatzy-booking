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
        'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        'title' => 'nullable|string|max:255',
        'price' => 'nullable|numeric|min:0',
        'display_order' => "required|integer|min:1|max:" . ($restaurant->photos()->count() + 1),
    ], [
        'photo.required' => 'You must select a photo to upload.',
        'photo.image' => 'The file must be an image.',
        'photo.mimes' => 'Allowed image types: jpeg, png, jpg, gif.',
        'photo.max' => 'Maximum file size is 2 MB.',
    ]);

    $file = $request->file('photo');
    $path = $file->store('restaurant_photos', 'public');

    $order = $request->input('display_order');
    RestaurantPhoto::where('restaurant_id', $restaurant->id)
        ->where('display_order', '>=', $order)
        ->increment('display_order');

    RestaurantPhoto::create([
        'restaurant_id' => $restaurant->id,
        'link' => $path,
        'display_order' => $order,
        'title' => $request->input('title'),
        'price' => $request->input('price'),
    ]);

    return redirect()->back()->with('success', 'Photo added successfully!');
}

    public function editPhotos($restaurantId)
    {
        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($restaurantId);

        if (!$user || !$user->isOwner() || $restaurant->owner_id !== $user->id) {
            abort(403);
        }

        $photos = $restaurant->photos()->orderBy('display_order')->get();

        return view('restaurants.edit_photos', compact('restaurant', 'photos'));
    }

    public function update(Request $request, $restaurantId, $photoId)
    {
        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($restaurantId);

        if (!$user || !$user->isOwner() || $restaurant->owner_id !== $user->id) {
            abort(403);
        }

        $photo = RestaurantPhoto::findOrFail($photoId);

        $maxOrder = $restaurant->photos()->count();

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'display_order' => "required|integer|min:1|max:$maxOrder",
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validated['display_order'] != $photo->display_order) {
            $oldOrder = $photo->display_order;
            $newOrder = $validated['display_order'];

            if ($newOrder < $oldOrder) {
                RestaurantPhoto::where('restaurant_id', $restaurant->id)
                    ->where('display_order', '>=', $newOrder)
                    ->where('display_order', '<', $oldOrder)
                    ->increment('display_order');
            } elseif ($newOrder > $oldOrder) {
                RestaurantPhoto::where('restaurant_id', $restaurant->id)
                    ->where('display_order', '<=', $newOrder)
                    ->where('display_order', '>', $oldOrder)
                    ->decrement('display_order');
            }

            $photo->display_order = $newOrder;
        }

        $photo->title = $request->input('title', $photo->title);
        $photo->price = $request->input('price', $photo->price);

        if ($request->hasFile('photo')) {
            Storage::disk('public')->delete($photo->link);
            $photo->link = $request->file('photo')->store('restaurant_photos', 'public');
        }

        $photo->save();

        return back()->with('success', 'Photo updated successfully!');
    }

    public function destroy($restaurantId, $photoId)
    {
        $user = Auth::user();
        $restaurant = Restaurant::findOrFail($restaurantId);

        if (!$user || !$user->isOwner() || $restaurant->owner_id !== $user->id) {
            abort(403);
        }

        $photo = RestaurantPhoto::findOrFail($photoId);
        $deletedOrder = $photo->display_order;

        if (Storage::disk('public')->exists($photo->link)) {
            Storage::disk('public')->delete($photo->link);
        }

        $photo->delete();

        RestaurantPhoto::where('restaurant_id', $restaurant->id)
            ->where('display_order', '>', $deletedOrder)
            ->decrement('display_order');

        return response()->json(['success' => true]);
    }
}