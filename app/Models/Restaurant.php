<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Restaurant extends Model
{
    protected $table = 'restaurant';   // SQL table name
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

   
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

   
    public function favouritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favourite', 'restaurant_id', 'user_id');
    }
}
