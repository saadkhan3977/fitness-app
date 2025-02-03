<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Conversation extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function user()
    {
        return $this->hasOne(\App\Models\User::class,'id','target_id');
    }
    
    // public function user_info()
    // {
    //     return $this->hasOne(\App\Models\User::class,'id',Auth::user()->id);
    // }
    
    public function message()
    {
        return $this->hasOne(\App\Models\Message::class,'chat_id','id')->select('id', 'chat_id','text')->orderBy('id','desc');
    }

    public function new_message()
	{
		return $this->hasMany(\App\Models\Message::class,'chat_id','id') // Adjust this if your model name is different
					->where('is_read', 0);
	}

    // public function messages()
    // {
    //     return $this->hasMany(Message::class);
    // }

    public function user_info()
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'first_name','last_name','photo');
    }

    public function target_user_info()
    {
        return $this->belongsTo(User::class, 'target_id')->select('id', 'first_name','last_name','photo');
    }
    
}
