<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\Reply;
use App\Models\Reservation;
use App\Policies\RestaurantPolicy;
use App\Policies\ReviewPolicy;
use App\Policies\ReplyPolicy;
use App\Policies\ReservationPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Restaurant::class, RestaurantPolicy::class);
        Gate::policy(Review::class, ReviewPolicy::class);
        Gate::policy(Reply::class, ReplyPolicy::class);
        Gate::policy(Reservation::class, ReservationPolicy::class);
    }
}
