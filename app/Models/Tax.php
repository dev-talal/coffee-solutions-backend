<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $guarded = [];

    /**
     * Get the created by user.
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
