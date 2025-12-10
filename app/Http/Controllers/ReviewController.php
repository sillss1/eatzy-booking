<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Database\QueryException;
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
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        try {
            Review::create([
                'user_id'       => $user->id,
                'restaurant_id' => $restaurant->id,
                'rating'        => $validated['rating'],
                'content'       => $validated['comment'],
            ]);
        } catch (QueryException $e) {
            $msg = $e->getMessage() ?? '';

            if (str_contains($msg, 'Users can only review restaurants where they have a completed reservation')) {
                return back()
                    ->withErrors([
                        'review' => 'You can only review restaurants where you have a completed reservation.',
                    ])
                    ->withInput();
            }

            throw $e;
        }

        return back()->with('success', 'Review added successfully.');
    }

    public function edit($id)
    {
        $review = Review::findOrFail($id);
        if (Auth::id() !== $review->user_id) abort(403);

        return view('reviews.edit', compact('review'));
    }

    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        if (Auth::id() !== $review->user_id) abort(403);

        $validated = $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        $review->update([
            'rating'  => $validated['rating'],
            'content' => $validated['comment'],
            'edited_at' => now(),
        ]);

        return redirect()
            ->route('restaurants.show', $review->restaurant_id)
            ->with('success', 'Review updated successfully.');
    }

    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        if (Auth::id() !== $review->user_id) abort(403);

        // podes fazer soft delete (meter deleted_at) ou delete direto:
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
