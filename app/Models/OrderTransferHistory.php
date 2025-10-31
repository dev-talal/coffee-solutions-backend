<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTransferHistory extends Model
{
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
