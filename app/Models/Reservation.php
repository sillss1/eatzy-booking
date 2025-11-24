<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    protected $table = 'reservation';
    protected $primaryKey = 'id';
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
        'is_completed'
    ];

    protected $dates = [
        'created_at',
        'edited_at',
        'deleted_at'
    ];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'is_completed' => 'boolean',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }


    public function getStatusAttribute()
    {
        if (!$this->is_confirmed && !$this->is_completed) {
            return 'pending';
        }

        if ($this->is_confirmed && !$this->is_completed) {
            return 'confirmed';
        }

        if ($this->is_confirmed && $this->is_completed) {
            return 'completed';
        }

        if (!$this->is_confirmed && $this->is_completed) {
            return 'cancelled';
        }
    }

    public function getIsModifiableAttribute()
    {
        return $this->status === 'pending' || $this->status === 'confirmed';
    }
    public function getIsDeletableAttribute()
    {
        return $this->status === 'completed' || $this->status === 'cancelled';
    }

}
