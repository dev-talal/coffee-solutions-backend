<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
