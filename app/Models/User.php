<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable {
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

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function replies()
    {
        return $this->hasMany(\App\Models\Reply::class, 'owner_id');
    }


    public $timestamps  = false;

    protected $table = 'user';

    protected $fillable = [
        'name',
        'surname',
        'username',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

}
