<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $guarded = [];

    public function warehouses()
    {
        return $this->belongsToMany(WareHouse::class, 'driver_warehouses', 'driver_id', 'warehouse_id');
    }
}
