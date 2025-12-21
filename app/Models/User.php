<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public function isOwner(): bool
    {
        return \DB::table('owner')->where('id', $this->id)->exists();
    }

    public function isCustomer(): bool
    {
        return \DB::table('customer')->where('id', $this->id)->exists();
    }

    public function isAdmin(): bool
    {
        return \DB::table('administrator')->where('id', $this->id)->exists();
    }

    public function favouriteRestaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'favourite', 'user_id', 'restaurant_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'user_id');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'user_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public $timestamps = false;

    protected $table = 'user';

    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'password',
        'is_blocked', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_blocked' => 'boolean'
    ];

}
