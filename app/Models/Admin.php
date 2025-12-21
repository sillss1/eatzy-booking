<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'administrator';
    public $timestamps = false;
    protected $fillable = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
