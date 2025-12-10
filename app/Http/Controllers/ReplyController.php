<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    public function store(Request $request, $reviewId)
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) abort(403);

        $review = Review::with('restaurant')->findOrFail($reviewId);

        if ($review->restaurant->owner_id !== $user->id) abort(403);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        Reply::create([
            'review_id' => $review->id,
            'owner_id'  => $user->id,
            'comment'   => $validated['comment'],
            'created_at'=> now(),
        ]);

        return back()->with('success', 'Reply posted successfully.');
    }

    public function edit($id)
    {
        $reply = Reply::with('review.restaurant')->findOrFail($id);
        $user = Auth::user();

        if (!$user || $user->id !== $reply->owner_id) abort(403);

        return view('replies.edit', compact('reply'));
    }

    public function update(Request $request, $id)
    {
        $reply = Reply::with('review.restaurant')->findOrFail($id);
        $user = Auth::user();

        if (!$user || $user->id !== $reply->owner_id) abort(403);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $reply->update([
            'comment'   => $validated['comment'],
            'edited_at' => now(),
        ]);

        return redirect()
            ->route('restaurants.show', $reply->review->restaurant_id)
            ->with('success', 'Reply updated successfully.');
    }

    public function destroy($id)
    {
        $reply = Reply::with('review.restaurant')->findOrFail($id);
        $user = Auth::user();

        if (!$user || $user->id !== $reply->owner_id) abort(403);

        $reply->delete();

        return back()->with('success', 'Reply deleted successfully.');
    }
}
