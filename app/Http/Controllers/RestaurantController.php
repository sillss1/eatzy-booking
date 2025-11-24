<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RestaurantController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user && $user->isOwner()) {
            $restaurants = Restaurant::active()
                ->where('owner_id', $user->id)
                ->orderBy('name')
                ->paginate(10);
        } else {
            $restaurants = Restaurant::active()
                ->orderBy('name')
                ->paginate(10);
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
        $user = Auth::user();
        if (!$user || !$user->isOwner()) abort(403);
        return view('restaurants.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) abort(403);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone_number'  => 'nullable|string|max:50',
            'address'       => 'required|string|max:255',
            'description'   => 'required|string',
            'capacity'      => 'required|integer|min:1',
            'mon_hours'     => 'nullable|string|max:255',
            'tue_hours'     => 'nullable|string|max:255',
            'wed_hours'     => 'nullable|string|max:255',
            'thu_hours'     => 'nullable|string|max:255',
            'fri_hours'     => 'nullable|string|max:255',
            'sat_hours'     => 'nullable|string|max:255',
            'sun_hours'     => 'nullable|string|max:255',
        ]);

        $opening_hours = $this->buildOpeningHoursFromRequest($request);

        Restaurant::create([
            'owner_id'      => $user->id,
            'name'          => $validated['name'],
            'description'   => $validated['description'],
            'email'         => $validated['email'],
            'phone_number'  => $validated['phone_number'] ?? null,
            'address'       => $validated['address'],
            'capacity'      => $validated['capacity'],
            'opening_hours' => $opening_hours,
            'created_at'    => Carbon::now(),
        ]);

        return redirect()->route('restaurants.index')->with('success', 'Restaurant created successfully!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) abort(403);

        $restaurant = Restaurant::findOrFail($id);
        if ($restaurant->owner_id !== $user->id) abort(403);

        return view('restaurants.edit', compact('restaurant'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) abort(403);

        $restaurant = Restaurant::findOrFail($id);
        if ($restaurant->owner_id !== $user->id) abort(403);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone_number'  => 'nullable|string|max:50',
            'address'       => 'required|string|max:255',
            'description'   => 'required|string',
            'capacity'      => 'required|integer|min:1',
            'mon_hours'     => 'nullable|string|max:255',
            'tue_hours'     => 'nullable|string|max:255',
            'wed_hours'     => 'nullable|string|max:255',
            'thu_hours'     => 'nullable|string|max:255',
            'fri_hours'     => 'nullable|string|max:255',
            'sat_hours'     => 'nullable|string|max:255',
            'sun_hours'     => 'nullable|string|max:255',
        ]);

        $opening_hours = $this->buildOpeningHoursFromRequest($request);

        $restaurant->update([
            'name'          => $validated['name'],
            'description'   => $validated['description'],
            'email'         => $validated['email'],
            'phone_number'  => $validated['phone_number'] ?? null,
            'address'       => $validated['address'],
            'capacity'      => $validated['capacity'],
            'opening_hours' => $opening_hours,
            'updated_at'    => Carbon::now(),
        ]);

        return redirect()->route('restaurants.show', $restaurant->id)->with('success', 'Restaurant updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user || !$user->isOwner()) abort(403);

        $restaurant = Restaurant::findOrFail($id);
        if ($restaurant->owner_id !== $user->id) abort(403);

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
