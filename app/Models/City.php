<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }
}
