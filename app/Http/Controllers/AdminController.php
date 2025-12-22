<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Owner;
use App\Models\Customer;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    // US47: Admin Dashboard
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'owners' => Owner::count(),
            'customers' => Customer::count(),
            'restaurants' => Restaurant::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    // US48 & US49: Manage Users (List & Search)
    public function listUsers(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%")
                    ->orWhere('username', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'owner') {
                $query->whereHas('owner');
            } elseif ($role === 'customer') {
                $query->whereHas('customer');
            } elseif ($role === 'admin') {
                $query->whereHas('admin');
            }
        }

        $query->where('id', '!=', Auth::id());

        $users = $query->orderByDesc('id')->paginate(20);

        return view('admin.users', compact('users'));
    }

    // US50: Delete User Account (Admin Action)
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->isAdmin()) {
            return back()->withErrors(['msg' => 'Cannot delete another administrator via this panel.']);
        }

        try {
            DB::transaction(function () use ($user) {
                $user->reviews()->update(['user_id' => null]);
                $user->replies()->update(['user_id' => null]);
                $user->reservations()->update(['user_id' => null]);
                // Laravel notifications deleted via CASCADE on user delete

                $user->favouriteRestaurants()->detach();

                $user->customer?->delete();
                $user->owner?->delete();

                $user->delete();
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

        $user->update(['is_blocked' => DB::raw('true')]);

        return back()->with('success', 'User blocked successfully.');
    }

    // Unblock User
    public function unblockUser($id)
    {
        $user = User::findOrFail($id);

        // No idea why, but setting this value to false only works with a full querry builder. Appearently this laravel version's common bug
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

    // Create User Form
    public function createUser()
    {
        return view('admin.users.create');
    }

    // Store new User
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:250',
            'surname' => 'required|string|max:250',
            'username' => 'required|string|max:250|unique:user',
            'email' => 'required|email|max:250|unique:user',
            'password' => 'required|string|min:8',
            'role' => 'required|in:customer,owner,admin',
        ]);

        User::createWithRole($validated);

        return redirect()->route('admin.users')->with('success', 'User created successfully.');
    }

    // ===== RESTAURANT MANAGEMENT =====

    public function listRestaurants(Request $request)
    {
        $query = Restaurant::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhere('address', 'ilike', "%{$search}%");
            });
        }

        $restaurants = $query->orderByDesc('id')->paginate(20);
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

        Restaurant::create([
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
        $restaurant = Restaurant::findOrFail($id);
        return view('admin.restaurants.edit', compact('restaurant'));
    }

    public function updateRestaurant(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);

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
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->closed_at = now();
        $restaurant->save();

        return back()->with('success', 'Restaurant removed from platform.');
    }

    // ===== REVIEW MANAGEMENT =====

    public function listReviews(Request $request)
    {
        $reviews = Review::with(['user', 'restaurant'])
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.resources', compact('reviews'))->with('tab', 'reviews');
    }

    public function editReview($id)
    {
        $review = Review::findOrFail($id);
        return view('admin.reviews.edit', compact('review'));
    }

    public function updateReview(Request $request, $id)
    {
        $review = Review::findOrFail($id);

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
        $review = Review::findOrFail($id);
        $review->delete();

        return back()->with('success', 'Review deleted successfully.');
    }
}
