<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Auth;

class Quote extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function images()
    {
        return $this->hasMany(QuoteImage::class,'quote_id','id');
    }

    public function user_info()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function negotiator_info()
    {
        return $this->hasOne(User::class,'id','negotiator_id');
    }

    public function review()
    {
//		echo Auth::user()->role;die;
        if(Auth::user()->role == 'Business Qbidder')
        {
            return $this->hasMany(Review::class,'quote_id','id')->where('assign_user_id',null);
        }
        else
        {
            return $this->hasMany(Review::class,'quote_id','id')->where('user_id',null);
        }
    }

    public function review_user_info()
    {
        return $this->hasOne(User::class,'id','quote_user_id');
    }

    public function bids() {
        return $this->hasMany(Bid::class);
    }

    public function bid() {
        return $this->hasOne(Bid::class);
    }


}
