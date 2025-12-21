<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Owner extends Model
{
    protected $table = 'owner';
    public $timestamps = false;
    protected $fillable = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function restaurants()
    {
        return $this->hasMany(Restaurant::class, 'owner_id');
    }
}
