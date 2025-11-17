<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
