<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantPhoto extends Model
{
    protected $table = 'restaurant_photo';

    protected $fillable = [
        'restaurant_id',
        'link',
        'display_order',
        'title',
        'price'
    ];

    public $timestamps = false;

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }
}