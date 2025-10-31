<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function cities()
    {
        return $this->hasMany(City::class, 'region_id');
    }
}
