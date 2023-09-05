<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VirutalWallet;
use App\Models\VirtualWalletTransaction;

class VirtualWalletController extends Controller
{
 
 
 
    public function addFunds(){
        return view('frontend.user.virtual_wallet.index');
    }
 
 
    public function deposit(Request $request)
    {
        // Deposit amount into the wallet for the authenticated user
        $user = auth()->user();
        $wallet = auth()->user()->virtual_wallet;
        
        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        $amount = $request->input('amount');

        // Update wallet balance
        $wallet->balance += $amount;
        $wallet->save();

        // Create a transaction record
        $transaction = new VirtualWalletTransaction();
        $transaction->virtual_wallet_id = $wallet->id;
        $transaction->type = 'deposit';
        $transaction->amount = $amount;
        $transaction->save();

        return response()->json(['message' => 'Deposit successful', 'data' => $wallet], 200);
    }
}
