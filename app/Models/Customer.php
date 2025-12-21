<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $table = 'customer';
    public $timestamps = false;
    protected $fillable = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
