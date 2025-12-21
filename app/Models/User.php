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
    public function updateProfile(array $data, ?\Illuminate\Http\UploadedFile $picture = null, bool $removePicture = false)
    {
        if ($removePicture && $this->profile_picture) {
            \Storage::disk('public')->delete($this->profile_picture);
            $this->profile_picture = null;
        }

        if (isset($data['username'])) $this->username = $data['username'];
        if (isset($data['email']) && $data['email'] !== $this->email) {
            $this->email = $data['email'];
        }
        $this->name = $data['name'] ?? $this->name;
        $this->surname = $data['surname'] ?? $this->surname;
        $this->profile_description = $data['profile_description'] ?? $this->profile_description;

        if ($picture) {
            if ($this->profile_picture) {
                \Storage::disk('public')->delete($this->profile_picture);
            }
            $this->profile_picture = $picture->store('profiles', 'public');
        }

        $this->save();
    }

    public function deleteAccount()
    {
        \DB::transaction(function () {
            $this->reviews()->update(['user_id' => null]);
            $this->replies()->update(['user_id' => null]);
            $this->reservations()->update(['user_id' => null]);
            $this->notifications()->update(['user_id' => null]);

            $this->favouriteRestaurants()->detach();

            $this->customer?->delete();
            $this->owner?->delete();
            $this->administrator?->delete();

            $this->delete();
        });
    }

    public function block(bool $state)
    {
        $this->is_blocked = $state;
        $this->save();
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
        'password' => 'hashed',
        'is_blocked' => 'boolean'
    ];

}
