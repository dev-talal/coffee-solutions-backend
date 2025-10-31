<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'customer_care_agent'];

    public function user()
    {
        return $this->belongsTo(User::class)->select('id', 'first_name', 'last_name', 'profile');
    }

    public function customerCareAgent()
    {
        return $this->belongsTo(User::class, 'customer_care_agent')
            ->select('id', 'first_name', 'last_name', 'profile');
    }

    public function messages(){
        return $this->hasMany(Message::class, 'room_id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'room_id')
            ->latestOfMany();
    }

    public function  getUnreadMessagesCount()
    {
        return $this->messages()->where('sender_id', '!=',  auth()->user()->id)->where('is_read', 0)->count();
    }

    public function getUnreadMessagesCountForCustomer()
    {
        return $this->messages()->where('is_read', 0)->where('sender_id', auth()->user()->id)->count();
    }
}