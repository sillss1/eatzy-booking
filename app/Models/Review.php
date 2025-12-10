<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table = 'review';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'content',
        'rating',
        'created_at',
        'edited_at',
        'deleted_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Se quiseres continuar a pensar em "customer":
    public function customer()
    {
        return $this->user();
    }

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}
