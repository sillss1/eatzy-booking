<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;

class RestaurantController extends Controller
{
    // US04 – restaurant list
    public function index()
    {
        $restaurants = Restaurant::orderBy('name')->paginate(10);
        return view('restaurants.index', compact('restaurants'));
    }

    // US05 – restaurant details
    public function show($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        return view('restaurants.show', compact('restaurant'));
    }
}
