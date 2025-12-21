<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // US47: Admin Dashboard
    public function index()
    {
        // Contagens simples para o dashboard
        $stats = [
            'users' => User::count(),
            'owners' => DB::table('owner')->count(),
            'customers' => DB::table('customer')->count(),
            'restaurants' => DB::table('restaurant')->count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // US48 & US49: Manage Users (List & Search)
    public function listUsers(Request $request)
    {
        $query = User::query();

        // Search by Name or Email - Full-Text and Exact-Match
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('username', 'ilike', "%{$search}%")

                    ->orWhere('name', $search)
                    ->orWhere('email', $search)
                    ->orWhere('username', $search);
            });
        }

        // Filter by Role
        if ($request->filled('role')) {
            if ($request->role === 'owner') {
                $query->whereIn('id', function ($q) {
                    $q->select('id')->from('owner');
                });
            } elseif ($request->role === 'customer') {
                $query->whereIn('id', function ($q) {
                    $q->select('id')->from('customer');
                });
            } elseif ($request->role === 'admin') {
                $query->whereIn('id', function ($q) {
                    $q->select('id')->from('administrator');
                });
            }
        }

        // Não mostrar o próprio admin na lista (para não se apagar a si mesmo)
        $query->where('id', '!=', Auth::id());

        $users = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.users', compact('users'));
    }

    // US50: Delete User Account (Admin Action)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Impedir apagar outros admins (opcional, mas seguro)
        if ($user->isAdmin()) {
            return back()->withErrors(['msg' => 'Cannot delete another administrator via this panel.']);
        }

        try {
            // Reutilizar a lógica robusta de anonimização (Hard Delete + Nullify)
            DB::transaction(function () use ($user) {
                DB::table('review')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('reply')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('reservation')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('waitlist')->where('user_id', $user->id)->update(['user_id' => null]);
                DB::table('notification')->where('user_id', $user->id)->update(['user_id' => null]);

                DB::table('favourite')->where('user_id', $user->id)->delete();
                DB::table('customer')->where('id', $user->id)->delete();
                DB::table('owner')->where('id', $user->id)->delete();

                // Finalmente apaga o user
                DB::table('user')->where('id', $user->id)->delete();
            });

            return back()->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['msg' => 'Error deleting user: ' . $e->getMessage()]);
        }
    }

    // Block User
    public function blockUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->withErrors(['msg' => 'Cannot block another administrator.']);
        }

        DB::table('user')->where('id', $id)->update(['is_blocked' => DB::raw('true')]);

        return back()->with('success', 'User blocked successfully.');
    }

    // Unblock User
    public function unblockUser($id)
    {
        $user = User::findOrFail($id);

        DB::table('user')->where('id', $id)->update(['is_blocked' => DB::raw('false')]);

        return back()->with('success', 'User unblocked successfully.');
    }

    // Edit User Form
    public function editUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->withErrors(['msg' => 'Cannot edit another administrator.']);
        }

        return view('admin.users.edit', compact('user'));
    }

    // Update User
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->withErrors(['msg' => 'Cannot edit another administrator.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'email' => ['required', 'email', 'max:255', \Illuminate\Validation\Rule::unique('user', 'email')->ignore($id)],
            'username' => ['required', 'string', 'max:50', \Illuminate\Validation\Rule::unique('user', 'username')->ignore($id)],
        ]);

        $user->update($validated);

        return redirect()->route('admin.users')->with('success', 'User updated successfully.');
    }

    // ===== RESTAURANT MANAGEMENT =====

    public function listRestaurants(Request $request)
    {
        $query = \App\Models\Restaurant::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('address', 'ilike', "%{$search}%");
            });
        }

        $restaurants = $query->orderBy('id', 'desc')->paginate(20);
        return view('admin.resources', compact('restaurants'))->with('tab', 'restaurants');
    }

    public function createRestaurant()
    {
        return view('admin.restaurants.create');
    }

    public function storeRestaurant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
        ]);

        \App\Models\Restaurant::create([
            'owner_id' => null,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'address' => $validated['address'],
            'description' => $validated['description'],
            'capacity' => $validated['capacity'],
            'opening_hours' => [
                'mon' => ['09:00-22:00'],
                'tue' => ['09:00-22:00'],
                'wed' => ['09:00-22:00'],
                'thu' => ['09:00-22:00'],
                'fri' => ['09:00-22:00'],
                'sat' => ['10:00-23:00'],
                'sun' => ['10:00-22:00'],
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('admin.resources')->with('success', 'Restaurant created successfully.');
    }

    public function editRestaurant($id)
    {
        $restaurant = \App\Models\Restaurant::findOrFail($id);
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function updateRestaurant(Request $request, $id)
    {
        $restaurant = \App\Models\Restaurant::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
        ]);

        $restaurant->update($validated);

        return redirect()->route('admin.resources')->with('success', 'Restaurant updated successfully.');
    }

    public function deleteRestaurant($id)
    {
        $restaurant = \App\Models\Restaurant::findOrFail($id);
        $restaurant->closed_at = now();
        $restaurant->save();

        return back()->with('success', 'Restaurant removed from platform.');
    }

    // ===== REVIEW MANAGEMENT =====

    public function listReviews(Request $request)
    {
        $reviews = \App\Models\Review::with(['user', 'restaurant'])
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.resources', compact('reviews'))->with('tab', 'reviews');
    }

    public function editReview($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        return view('admin.reviews.edit', compact('review'));
    }

    public function updateReview(Request $request, $id)
    {
        $review = \App\Models\Review::findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string|max:2000',
        ]);

        $review->update([
            'rating' => $validated['rating'],
            'content' => $validated['content'],
            'edited_at' => now(),
        ]);

        return redirect()->route('admin.reviews')->with('success', 'Review updated successfully.');
    }

    public function deleteReview($id)
    {
        $review = \App\Models\Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
