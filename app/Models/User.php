<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
    
    public function isOwner(): bool
    {
        return \DB::table('owner')->where('id', $this->id)->exists();
    }

    
    public function isAdmin(): bool
    {
        return \DB::table('administrator')->where('id', $this->id)->exists();
    }
{
   
    use HasFactory, Notifiable;

    
    public $timestamps  = false;

    
     @var list<string>
     
    protected $table = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

   
     @var list<string>
     
    protected $hidden = [
        'password',
        'remember_token',
    ];

    
     return array<string, string>
     
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
           
            'password' => 'hashed',
        ];
    }


    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
