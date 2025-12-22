<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Restaurant::active();

        if ($user && $user->isOwner()) {
            $query->where('owner_id', $user->id);
        }

        // Full-text and exact match search

        $search = trim($request->get('search'));

        if ($search) {
            $searchTerms = preg_split('/\s+/', $search);

            $tsQuery = implode(' | ', $searchTerms);

            $query->where(function ($q) use ($searchTerms, $tsQuery) {
                $q->whereRaw("tsvectors @@ to_tsquery('english', ?)", [$tsQuery])
                ->orWhere(function ($q2) use ($searchTerms) {
                    foreach ($searchTerms as $term) {
                        $q2->orWhere('name', 'ILIKE', "%{$term}%")
                            ->orWhere('description', 'ILIKE', "%{$term}%")
                            ->orWhere('address', 'ILIKE', "%{$term}%");
                    }
                });
            })
            ->orderByRaw("ts_rank(tsvectors, to_tsquery('english', ?)) DESC", [$tsQuery])
            ->orderByRaw("name = ? DESC", [$search])
            ->orderByRaw("address = ? DESC", [$search]);
        }

        $direction = $request->get('direction', 'asc');

        // Favourite filtering

        $onlyFavourites = $request->boolean('only_favourites', false);

        if ($onlyFavourites && $user) {
            $favouriteIds = $user->favouriteRestaurants()->pluck('id')->toArray();
            if (!empty($favouriteIds)) {
                $query->whereIn('id', $favouriteIds);
            } else {
                $query->whereRaw('0 = 1');
            }
        }

        // Sorting

        if ($request->filled('sort')) {

            if ($request->sort === 'name') {
                $query->orderBy('name', $direction);
            }

            if ($request->sort === 'address') {
                $query->orderBy('address', $direction);
            }

            if ($request->sort === 'description') {
                $query->orderBy('description', $direction);
            }

            if ($request->sort === 'capacity') {
                $query->orderBy('capacity', $direction);
            }

            if ($request->sort === 'created_at') {
                $query->orderBy('created_at', $direction);
            }

        } else {
            $query->orderBy('name', 'asc');
        }

        $restaurants = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('restaurants._list', compact('restaurants'))->render()
            ]);
        }

        return view('restaurants.index', compact('restaurants'));
    }

    public function show($id)
    {
        $restaurant = Restaurant::active()->findOrFail($id);
        return view('restaurants.show', compact('restaurant'));
    }

    public function create()
    {
        $this->authorize('create', Restaurant::class);
        return view('restaurants.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', Restaurant::class);
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'mon_hours' => 'nullable|string|max:255',
            'tue_hours' => 'nullable|string|max:255',
            'wed_hours' => 'nullable|string|max:255',
            'thu_hours' => 'nullable|string|max:255',
            'fri_hours' => 'nullable|string|max:255',
            'sat_hours' => 'nullable|string|max:255',
            'sun_hours' => 'nullable|string|max:255',
        ]);

        $opening_hours = $this->buildOpeningHoursFromRequest($request);

        $hasAtLeastOneDayOpen = false;
        foreach ($opening_hours as $hours) {
            if (!empty($hours)) {
                $hasAtLeastOneDayOpen = true;
                break;
            }
        }

        if (!$hasAtLeastOneDayOpen) {
            return back()
                ->withErrors(['opening_hours' => 'The restaurant must be open at least one day per week.'])
                ->withInput();
        }


        Restaurant::create([
            'owner_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'],
            'capacity' => $validated['capacity'],
            'opening_hours' => $opening_hours,
            'created_at' => Carbon::now(),
        ]);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant created successfully!');
    }

    public function edit($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->authorize('update', $restaurant);

        return view('restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, $id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->authorize('update', $restaurant);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'nullable|string|max:50',
            'address' => 'required|string|max:255',
            'description' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'mon_hours' => 'nullable|string|max:255',
            'tue_hours' => 'nullable|string|max:255',
            'wed_hours' => 'nullable|string|max:255',
            'thu_hours' => 'nullable|string|max:255',
            'fri_hours' => 'nullable|string|max:255',
            'sat_hours' => 'nullable|string|max:255',
            'sun_hours' => 'nullable|string|max:255',
        ]);

        $opening_hours = $this->buildOpeningHoursFromRequest($request);

        $hasAtLeastOneDayOpen = false;
        foreach ($opening_hours as $hours) {
            if (!empty($hours)) {
                $hasAtLeastOneDayOpen = true;
                break;
            }
        }

        if (!$hasAtLeastOneDayOpen) {
            return back()
                ->withErrors(['opening_hours' => 'The restaurant must be open at least one day per week.'])
                ->withInput();
        }



        $restaurant->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'address' => $validated['address'],
            'capacity' => $validated['capacity'],
            'opening_hours' => $opening_hours,
            'updated_at' => Carbon::now(),
        ]);

        return redirect()->route('restaurants.show', $restaurant->id)->with('success', 'Restaurant updated successfully!');
    }

    public function destroy($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $this->authorize('delete', $restaurant);

        $restaurant->closed_at = Carbon::now();
        $restaurant->save();

        return redirect()->route('restaurants.index')->with('success', 'Restaurant removed from the platform.');
    }

    protected function buildOpeningHoursFromRequest(Request $request): array
    {
        $map = [
            'mon_hours' => 'mon',
            'tue_hours' => 'tue',
            'wed_hours' => 'wed',
            'thu_hours' => 'thu',
            'fri_hours' => 'fri',
            'sat_hours' => 'sat',
            'sun_hours' => 'sun',
        ];

        $opening_hours = [];

        foreach ($map as $field => $key) {
            $value = trim((string) $request->input($field, ''));
            $opening_hours[$key] = $value === '' ? [] : array_map('trim', explode(',', $value));
        }

        return $opening_hours;
    }
}
