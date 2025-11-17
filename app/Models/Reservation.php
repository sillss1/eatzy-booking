<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    protected $table = 'reservation';
    protected $primaryKey = 'id';

    // Disable Laravel timestamps (A8 requirement 3.8)
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'title',
        'description',
        'number_of_people',
        'date_of_visit',
        'time_of_visit',
        'is_confirmed',
        'is_completed',
        'created_at',
        'edited_at',
        'deleted_at',
    ];

    protected $casts = [
        'date_of_visit' => 'date',
        'time_of_visit' => 'datetime:H:i',
        'is_confirmed' => 'boolean',
        'is_completed' => 'boolean',
    ];

    /**
     * Get the user that owns the reservation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the restaurant for this reservation.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Check if reservation is cancelled (soft delete).
     */
    public function isCancelled(): bool
    {
        return $this->deleted_at !== null;
    }

    /**
     * Check if reservation can be edited (not completed and not in the past).
     */
    public function canBeEdited(): bool
    {
        return !$this->is_completed && 
               !$this->isCancelled() &&
               $this->date_of_visit >= now()->toDateString();
    }
}
