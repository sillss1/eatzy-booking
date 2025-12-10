<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, $restaurantId)
    {
        $user = Auth::user();
        if (!$user || !$user->isCustomer()) abort(403);

        $restaurant = Restaurant::active()->findOrFail($restaurantId);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        Review::create([
            'customer_id' => $user->id,
            'restaurant_id' => $restaurant->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return back()->with('success', 'Review added successfully.');
    }

    public function edit($id)
    {
        $review = Review::findOrFail($id);
        if (Auth::id() !== $review->customer_id) abort(403);

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        if (Auth::id() !== $review->customer_id) abort(403);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        $review->update($validated);

        return redirect()->route('restaurants.show', $review->restaurant_id)
            ->with('success', 'Review updated successfully.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        if (Auth::id() !== $review->customer_id) abort(403);

        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
