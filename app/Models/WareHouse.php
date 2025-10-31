<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WareHouse extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function warehouseRegions()
    {
        return $this->belongsToMany(Region::class, 'warehouse_regions', 'warehouse_id', 'region_id');
    }
}
