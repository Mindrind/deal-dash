<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VirtualWalletTransaction extends Model
{
    use HasFactory;

    protected $table = 'virtual_wallet_transactions';


    protected $fillable = ['virtual_wallet_id', 'type', 'amount'];
    
    public function virtualWallet()
    {
        return $this->belongsTo(VirtualWallet::class, 'virtual_wallet_id');
    }


}
