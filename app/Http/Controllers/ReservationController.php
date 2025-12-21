<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Notifications\ReservationCreated;
use App\Notifications\ReservationUpdated;
use App\Notifications\ReservationCancelled;
use App\Notifications\ReservationConfirmed;
use App\Models\User;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isOwner() && !$user->isAdmin()) {

            $restaurants = Restaurant::where('owner_id', $user->id)->orderBy('id')->get();

            // Handle case when owner has no restaurants
            if ($restaurants->isEmpty()) {
                return view('reservations.index', [
                    'reservations' => collect(),
                    'restaurants' => $restaurants,
                    'selectedRestaurant' => null,
                    'capacityMap' => []
                ]);
            }

            $selectedRestaurant = $request->get('restaurant_id');

            if ($restaurants->count() === 1) {
                $selectedRestaurant = $restaurants->first()->id;
            } else {
                $selectedRestaurant = $request->get('restaurant_id') ?? $restaurants->first()->id;
            }

            $query = Reservation::query()
                ->where('restaurant_id', $selectedRestaurant)
                ->whereNotNull('user_id')
                ->whereNotNull('restaurant_id')
                ->with(['restaurant', 'user']);

        } else {
            $query = Reservation::query()
                ->where('user_id', $user->id)
                ->whereNotNull('user_id')
                ->whereNotNull('restaurant_id')
                ->with(['restaurant', 'user']);
        }

        $search = $request->get('search');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ILIKE', "%{$search}%")
                    ->orWhere('description', 'ILIKE', "%{$search}%")
                    ->orWhere('date_of_visit', 'ILIKE', "%{$search}%");

                $q->orWhereHas('user', function ($q2) use ($search) {
                    $q2->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('surname', 'ILIKE', "%{$search}%");
                });
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
                        WHEN is_confirmed = false AND is_completed = false THEN 1
                        WHEN is_confirmed = true  AND is_completed = false THEN 2
                        WHEN is_confirmed = false AND is_completed = true  THEN 3
                        WHEN is_confirmed = true  AND is_completed = true  THEN 4
                    END $direction
                ");
            }
        } else {
            $query->orderByRaw("
                CASE
                    WHEN is_confirmed = false AND is_completed = false THEN 1
                    WHEN is_confirmed = true  AND is_completed = false THEN 1
                    WHEN is_confirmed = false AND is_completed = true  THEN 2
                    WHEN is_confirmed = true  AND is_completed = true  THEN 2
                END $direction
            ");

            $query->orderBy('date_of_visit', 'asc')
                ->orderBy('time_of_visit', 'asc');
        }

        if ($request->filled('date')) {
            $query->whereDate('date_of_visit', $request->date);
        }

        $reservations = $query->get();

        $status = $request->get('status', 'current');

        if ($status && $status !== 'all') {
            $reservations = $reservations->filter(function ($r) use ($status) {
                if ($status === 'current') {
                    return in_array($r->status, ['pending', 'confirmed']);
                } elseif ($status === 'past') {
                    return in_array($r->status, ['cancelled', 'completed']);
                } else {
                    return $r->status === $status;
                }
            });
        }

        $currentCapacity = [];

        foreach ($reservations as $res) {

            $key = $res->restaurant_id . '|' . $res->date_of_visit;

            if (!isset($currentCapacity[$key])) {
                $currentCapacity[$key] = Reservation::where('restaurant_id', $res->restaurant_id)
                    ->where('date_of_visit', $res->date_of_visit)
                    ->where(function ($q) {
                        $q->whereRaw('is_confirmed = true AND is_completed = false');
                    })
                    ->sum('number_of_people');
            }
        }

        if ($request->ajax()) {
            return response()->json([
                'html' => view('reservations._list', [
                    'reservations' => $reservations,
                    'capacityMap' => $currentCapacity,
                ])->render()
            ]);
        }

        return view('reservations.index', [
            'reservations' => $reservations,
            'restaurants' => $restaurants ?? null,
            'selectedRestaurant' => $selectedRestaurant ?? null,
            'capacityMap' => $currentCapacity
        ]);
    }

    public function show($id)
    {
        $reservation = Reservation::with('restaurant')->findOrFail($id);

        $user = Auth::user();
        if (!$user->isAdmin() && $reservation->user_id !== $user->id && $reservation->restaurant->owner_id !== $user->id) {
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

            $owner = User::find($restaurant->owner_id);
            if ($owner) {
                $owner->notify(new ReservationCreated([
                    'title' => 'New reservation request',
                    'message' => 'A new reservation was created for ' . $restaurant->name . '.',
                    'url' => route('reservations.show', $reservation->id),
                    'reservation_id' => $reservation->id,
                    'restaurant_id' => $restaurant->id,
                ]));
            }

            Auth::user()->notify(new ReservationCreated([
                'title' => 'Reservation created',
                'message' => 'Your reservation at ' . $restaurant->name . ' was created.',
                'url' => route('reservations.show', $reservation->id),
                'reservation_id' => $reservation->id,
                'restaurant_id' => $restaurant->id,
            ]));

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

        if (!$reservation->is_modifiable) {
            return redirect()->route('reservations.index')->with('error', 'Only reservations that have not been completed can be edited.');
        }

        $restaurant = $reservation->restaurant;

        return view('reservations.edit', compact('reservation', 'restaurant'));
    }

    public function update(Request $request, $id)
    {
        $reservation = Reservation::with('restaurant')->findOrFail($id);

        if ($reservation->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You cannot edit this reservation.');
        }

        if (!$reservation->is_modifiable) {
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

            $restaurant = $reservation->restaurant;
            $owner = User::find($restaurant->owner_id);

            if ($owner) {
                $owner->notify(new ReservationUpdated([
                    'title' => 'Reservation updated',
                    'message' => 'A reservation was updated for ' . $restaurant->name . '.',
                    'url' => route('reservations.show', $reservation->id),
                    'reservation_id' => $reservation->id,
                    'restaurant_id' => $restaurant->id,
                ]));
            }

            $reservationUser = User::find($reservation->user_id);
            if ($reservationUser) {
                $reservationUser->notify(new ReservationUpdated([
                    'title' => 'Your reservation was updated',
                    'message' => 'Your reservation at ' . $restaurant->name . ' was updated.',
                    'url' => route('reservations.show', $reservation->id),
                    'reservation_id' => $reservation->id,
                    'restaurant_id' => $restaurant->id,
                ]));
            }

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
        $reservation = Reservation::with('restaurant')->findOrFail($id);
        $user = Auth::user();

        if ($reservation->user_id !== $user->id && !($user->isOwner() && $reservation->restaurant->owner_id === $user->id) && !$user->isAdmin()) {
            abort(403, 'You cannot cancel this reservation.');
        }

        if (!$reservation->is_modifiable) {
            return redirect()->back()->with('error', 'Only reservations that are pending or confirmed can be cancelled.');
        }

        $reservation->is_confirmed = DB::raw('FALSE');
        $reservation->is_completed = DB::raw('TRUE');
        $reservation->save();

        $restaurant = $reservation->restaurant;
        $owner = User::find($restaurant->owner_id);

        if ($owner) {
            $owner->notify(new ReservationCancelled([
                'title' => 'Reservation cancelled',
                'message' => 'A reservation at ' . $restaurant->name . ' was cancelled.',
                'url' => route('reservations.index'),
                'reservation_id' => $reservation->id,
                'restaurant_id' => $restaurant->id,
            ]));
        }

        $reservationUser = User::find($reservation->user_id);
        if ($reservationUser) {
            $reservationUser->notify(new ReservationCancelled([
                'title' => 'Reservation cancelled',
                'message' => 'Your reservation at ' . $restaurant->name . ' was cancelled.',
                'url' => route('reservations.index'),
                'reservation_id' => $reservation->id,
                'restaurant_id' => $restaurant->id,
            ]));
        }

        return redirect()->back()->with('success', 'Reservation cancelled successfully.');
    }

    public function confirm($id)
    {
        $reservation = Reservation::with('restaurant')->findOrFail($id);
        $user = Auth::user();

        if (!$user->isOwner() || $reservation->restaurant->owner_id !== $user->id) {
            abort(403);
        }

        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending reservations can be confirmed.');
        }

        $reservation->is_confirmed = DB::raw('TRUE');
        $reservation->save();

        $restaurant = $reservation->restaurant;
        $reservationUser = User::find($reservation->user_id);

        if ($reservationUser) {
            $reservationUser->notify(new ReservationConfirmed([
                'title' => 'Reservation confirmed',
                'message' => 'Your reservation at ' . $restaurant->name . ' was confirmed.',
                'url' => route('reservations.show', $reservation->id),
                'reservation_id' => $reservation->id,
                'restaurant_id' => $restaurant->id,
            ]));
        }

        return redirect()->back()->with('success', 'Reservation confirmed.');
    }

    public function destroy($id)
    {
        $reservation = Reservation::with('restaurant')->findOrFail($id);

        if ($reservation->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You cannot delete this reservation.');
        }

        if (!$reservation->is_deletable) {
            return redirect()->back()->with('error', 'Only completed or cancelled reservations can be deleted.');
        }

        $restaurant = $reservation->restaurant;
        $owner = User::find($restaurant->owner_id);

        if ($owner) {
            $owner->notify(new ReservationCancelled([
                'title' => 'Reservation deleted',
                'message' => 'A reservation at ' . $restaurant->name . ' was deleted.',
                'url' => route('reservations.index'),
                'reservation_id' => $reservation->id,
                'restaurant_id' => $restaurant->id,
            ]));
        }

        $reservationUser = User::find($reservation->user_id);
        if ($reservationUser) {
            $reservationUser->notify(new ReservationCancelled([
                'title' => 'Reservation deleted',
                'message' => 'Your reservation at ' . $restaurant->name . ' was deleted.',
                'url' => route('reservations.index'),
                'reservation_id' => $reservation->id,
                'restaurant_id' => $restaurant->id,
            ]));
        }

        $reservation->forceDelete();

        return redirect()->route('reservations.index')->with('success', 'Reservation deleted successfully.');
    }
}
