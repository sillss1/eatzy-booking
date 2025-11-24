<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    public function getRestaurants(Request $request)
    {
        // TODO: Implement actual search logic
        return response()->json(['restaurants' => []], 200);
    }

    public function updateReservation(Request $request, $id)
    {
        // TODO: Implement update logic
        return response()->json(['success' => true], 200);
    }

    public function deleteReservation(Request $request, $id)
    {
        // TODO: Implement delete logic
        return response()->json(null, 204);
    }

    public function deleteReview(Request $request, $id)
    {
        // TODO: Implement delete logic
        return response()->json(['id' => $id, 'deleted_at' => now()->toDateString()], 200);
    }

    public function deletePhoto(Request $request, $id)
    {
        // TODO: Implement delete logic
        return response()->json(null, 204);
    }
}
