<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notification';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'date',
        'viewed'
    ];

    protected $casts = [
        'viewed' => 'boolean',
        'date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
