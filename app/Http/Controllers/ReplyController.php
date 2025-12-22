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
        $review = Review::with('restaurant')->findOrFail($reviewId);
        $this->authorize('create', [Reply::class, $review]);
        $user = Auth::user();

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        Reply::create([
            'review_id' => $review->id,
            'user_id' => $user->id,
            'content' => $validated['comment'],
            'created_at' => now(),
        ]);

        return back()->with('success', 'Reply posted successfully.');
    }

    public function edit($id)
    {
        $reply = Reply::with('review.restaurant')->findOrFail($id);
        $this->authorize('update', $reply);

        return view('replies.edit', compact('reply'));
    }

    public function update(Request $request, $id)
    {
        $reply = Reply::with('review.restaurant')->findOrFail($id);
        $this->authorize('update', $reply);

        $validated = $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $reply->update([
            'content' => $validated['comment'],
            'edited_at' => now(),
        ]);

        return redirect()
            ->route('restaurants.show', $reply->review->restaurant_id)
            ->with('success', 'Reply updated successfully.');
    }

    public function destroy($id)
    {
        $reply = Reply::with('review.restaurant')->findOrFail($id);
        $this->authorize('delete', $reply);

        $reply->delete();

        return back()->with('success', 'Reply deleted successfully.');
    }
}
