<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// Import Eloquent relationship classes.
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
    /**
     * Check if the user is an owner.
     */
    public function isOwner(): bool
    {
        return \DB::table('owner')->where('id', $this->id)->exists();
    }

    /**
     * Check if the user is an admin.
     */
    public function isAdmin(): bool
    {
        return \DB::table('administrator')->where('id', $this->id)->exists();
    }
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Disable default created_at and updated_at timestamps for this model.
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable.
     *
     * Only these fields may be filled using methods like create() or update().
     * This protects against mass-assignment vulnerabilities.
     *
     * @var list<string>
     */
    protected $table = 'user';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden when serializing the model
     * (e.g., to arrays or JSON).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to a specific type.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            // Ensures password is always hashed automatically when set.
            'password' => 'hashed',
        ];
    }


    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
