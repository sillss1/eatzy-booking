<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Favourite extends Pivot
{
    protected $table = 'favourite';
    public $timestamps = false;
}
