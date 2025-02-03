<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuoteHelp extends Model
{
    use HasFactory;
    protected $guarded =[];
    
    public function bid() {
        return $this->hasOne(Bid::class,'quote_id','id');
    }
    
    public function user_info()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function negotiator_info()
    {
        return $this->hasOne(User::class,'id','negotiator_id');
    }
}
