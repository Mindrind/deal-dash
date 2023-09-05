<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualWallet extends Model
{

    protected $table = 'virtual_wallets';
    
    protected $fillable = ['user_id', 'balance']; 


    public function user(){
    	return $this->belongsTo(User::class);
    }
}

