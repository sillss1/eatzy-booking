<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


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

    protected $casts = [
        'opening_hours' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime', 
        'closed_at' => 'datetime'
    ];
   
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'restaurant_id');
    }

    public function photos()
    {
        return $this->hasMany(RestaurantPhoto::class, 'restaurant_id')->orderBy('display_order');
    }

    public function favouritedByUsers()
    {
        return $this->belongsToMany(User::class, 'favourite', 'restaurant_id', 'user_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('closed_at');
    }

    public function getFormattedOpeningHoursAttribute(): array
    {
        $days = [
            'Monday'    => 'mon',
            'Tuesday'   => 'tue',
            'Wednesday' => 'wed',
            'Thursday'  => 'thu',
            'Friday'    => 'fri',
            'Saturday'  => 'sat',
            'Sunday'    => 'sun',
        ];

        $formatted = [];
        $opening_hours = $this->opening_hours ?? [];

        foreach ($days as $label => $key) {
            $hours = $opening_hours[$key] ?? [];
            if (!is_array($hours)) $hours = [$hours];
            $formatted[$label] = count($hours) ? implode(', ', $hours) : 'Closed';
        }

        return $formatted;
    }

}