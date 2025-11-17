<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected $table = 'restaurant';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'email',
        'phone_number',
        'address',
        'opening_hours',
        'capacity',
        'created_at',
        'updated_at',
        'closed_at',
    ];

    /**
     * Get the owner (user) of this restaurant.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get all reservations for this restaurant.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'restaurant_id');
    }

    /**
     * Get all reviews for this restaurant.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'restaurant_id');
    }

    /**
     * Check if restaurant is closed.
     */
    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }
}

