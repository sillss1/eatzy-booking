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
        return $this->hasMany(Reservation::class);
    }

   
    public function favouritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favourite', 'restaurant_id', 'user_id');
    }

    public function getFormattedOpeningHoursAttribute(): array
    {
        $days = [
            'Monday' => 'mon',
            'Tuesday' => 'tue', 
            'Wednesday' => 'wed',
            'Thursday' => 'thu',
            'Friday' => 'fri',
            'Saturday' => 'sat',
            'Sunday' => 'sun',
        ];

        $formatted = [];

        foreach ($days as $label => $key) {
            $hours = $this->opening_hours[$key] ?? [];
            
            if (count($hours) === 0) {
                $formatted[$label] = 'Closed';
            } else {
                $formatted[$label] = implode(', ', $hours);
            }
        }

        return $formatted;
    }
}
