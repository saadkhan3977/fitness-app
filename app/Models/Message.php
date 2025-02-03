<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function user()
    {
        return $this->hasOne(\App\Models\User::class,'id','target_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'chat_id');
    }

    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id');
    // }

    

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_id');
    }

    protected $casts = [
        'user' => 'array',
    ];
}
