<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;


class ReservationController extends Controller
{
   public function index(Request $request)
    {
        $query = Reservation::query()
            ->where('user_id', Auth::id())
            ->with('restaurant');

        $includePast = $request->boolean('include_past', false);

        if (! $includePast) {
            $query->where(function ($q) {
                $q->whereRaw('is_confirmed = false AND is_completed = false')
                ->orWhereRaw('is_confirmed = true AND is_completed = false');
            });
        }

        $direction = $request->get('direction', 'asc');

        if ($request->filled('sort')) {

            if ($request->sort === 'restaurant_name') {
                $query->orderBy(
                    Restaurant::select('name')
                        ->whereColumn('restaurant.id', 'reservation.restaurant_id'),
                    $direction
                );
            }

            if ($request->sort === 'date') {
                $query->orderBy('date_of_visit', $direction)
                    ->orderBy('time_of_visit', $direction);
            }

            if ($request->sort === 'title') {
            $query->orderBy('title', $direction);
            }

        
            if ($request->sort === 'created_at') {
                $query->orderBy('created_at', $direction);
            }

            if ($request->sort === 'status') {
                $query->orderByRaw("
                    CASE
                        WHEN is_confirmed = false AND is_completed = false THEN 1 -- pending
                        WHEN is_confirmed = true  AND is_completed = false THEN 2 -- confirmed
                        WHEN is_confirmed = false AND is_completed = true  THEN 3 -- cancelled
                        WHEN is_confirmed = true  AND is_completed = true  THEN 4 -- completed
                    END $direction
                ");
            }
        } else {
            $query->orderByRaw("
                CASE
                    WHEN is_confirmed = false AND is_completed = false THEN 1 -- pending
                    WHEN is_confirmed = true  AND is_completed = false THEN 1 -- confirmed
                    WHEN is_confirmed = false AND is_completed = true  THEN 2 -- cancelled
                    WHEN is_confirmed = true  AND is_completed = true  THEN 2 -- completed
                END $direction
            ");

            $query->orderBy('date_of_visit', 'asc')
                ->orderBy('time_of_visit', 'asc');
        }

        $reservations = $query->get();

        return view('reservations.index', compact('reservations'));
    }


    public function show($id)
    {
        $reservation = Reservation::with('restaurant')->findOrFail($id);

        if (! Auth::user()->isAdmin() && Auth::id() !== $reservation->user_id) {
            abort(403, 'You cannot entre this site');
        }

        return view('reservations.show', compact('reservation'));
    }

    public function create($restaurant_id)
    {
        $restaurant = Restaurant::findOrFail($restaurant_id);

        return view('reservations.create', compact('restaurant'));
    }

    public function store(Request $request, $restaurant_id)
    {
        $restaurant = Restaurant::findOrFail($restaurant_id);

        $request->validate([
            'number_of_people' => 'required|integer|min:1',
            'date_of_visit' => 'required|date|after_or_equal:today',
            'time_of_visit' => 'required',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            $reservation = Reservation::create([
                'user_id' => Auth::id(),
                'restaurant_id' => $restaurant->id,
                'title' => $request->title ?? 'Reservation',
                'description' => $request->description,
                'number_of_people' => $request->number_of_people,
                'date_of_visit' => $request->date_of_visit,
                'time_of_visit' => $request->time_of_visit,
            ]);

            return redirect()->route('reservations.index')
            ->with('success', 'Reservation created.');
        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/ERROR:\s+(.*)/', $errorMessage, $matches)) {
                $errorMessage = $matches[1];
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    public function edit($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You cannot edit this reservation.');
        }

        if (! $reservation->is_editable) {
            return redirect()->route('reservations.index')->with('error', 'Only reservations that have not been completed can be edited.');
        }

        $restaurant = $reservation->restaurant;

        return view('reservations.edit', compact('reservation', 'restaurant'));
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You cannot edit this reservation.');
        }

        if (! $reservation->is_editable) {
            return redirect()->route('reservations.index')
                ->with('error', 'Only reservations that have not been completed can be edited.');
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'number_of_people' => 'required|integer|min:1',
            'date_of_visit' => 'required|date|after_or_equal:today',
            'time_of_visit' => 'required',
        ]);

        try {
            $reservation->title = $request->title ?? 'Reservation';
            $reservation->description = $request->description;
            $reservation->number_of_people = $request->number_of_people;
            $reservation->date_of_visit = $request->date_of_visit;
            $reservation->time_of_visit = $request->time_of_visit;
            $reservation->edited_at = now();

            if ($reservation->is_confirmed) {
                $reservation->is_confirmed = DB::raw('FALSE');
            }

            $reservation->save();
            
            return redirect()->route('reservations.show', $reservation->id)->with('success', 'Reservation edited successfully.');

        } catch (QueryException $e) {
            $errorMessage = $e->getMessage();
            if (preg_match('/ERROR:\s+(.*)/', $errorMessage, $matches)) {
                $errorMessage = $matches[1];
            }

            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }
    }

    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id()) {
            abort(403, 'You cannot cancel this reservation.');
        }

        if (! $reservation->is_cancellable) {
            return redirect()->back()->with('error', 'Only reservations that are pending or confirmed can be cancelled.');
        }

        $reservation->is_confirmed = DB::raw('FALSE');
        $reservation->is_completed = DB::raw('TRUE');
        $reservation->save();

        return redirect()->route('reservations.index')->with('success', 'Reservation cancelled successfully.');
    }


    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);

        if ($reservation->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You cannot delete this reservation.');
        }

        if (! $reservation->is_deletable) {
            return redirect()->back()->with('error', 'Only completed or cancelled reservations can be deleted.');
        }

        $reservation->forceDelete();

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
