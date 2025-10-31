<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffWarehouse extends Model
{
    protected $guarded = [];

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }
}
