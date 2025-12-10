<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    protected $table = 'reply';

    public $timestamps = false;

    protected $fillable = [
        'review_id',
        'owner_id',
        'comment',
        'created_at',
        'edited_at',
        'deleted_at',
    ];

    public function review()
    {
        return $this->belongsTo(Review::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
