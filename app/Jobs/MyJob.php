<?php
 
namespace App\Jobs;
 
use App\Models\Podcast;
use App\Services\AudioProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\AuctionProductBid;
 
class MyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
 

 
    /**
     * Execute the job.
     */
    public function handle()
    {
        $continueLoop = true;
        
        
        while ($continueLoop) {
            
            $auctionProducts = AuctionProductBid::all();
            // $auctionProducts = AuctionProductBid::where('product_id', 18)->first();
    
            $products = $this->filterExpiredBids($auctionProducts);

            foreach($products as $product){
                $this->placeBidByBot($product);
            }
            sleep(10);
            
            // Check if we need to exit the loop
            // if (9999 < time()) {
            //     $continueLoop = false;
            // }
        }
    }


    
    private function randomBotUser()
    {
        $randomId = rand(11, 20);
        return User::find($randomId);
    }


    private function filterExpiredBids($products){


        $activeAuctionProducts = [];
   
        foreach($products as $product){
     
            if(!$this->isAuctionExpired($product)){
                $activeAuctionProducts[] = $product;
            }
        }
        return $activeAuctionProducts;
    }   


    private function isAuctionExpired($product) {
     
        $currentTime = time(); // Get the current Unix timestamp
        $auctionTime = $product->product->auction_end_date; // Convert the date string to a Unix timestamp
        if ($auctionTime < $currentTime) {
            return true; // The auction has expired
        } else {
            return false; // The auction is still active
        }
    }

    private function placeBidByBot ($product) {
        $user = $this->randomBotUser();
        

        $bidByBot = AuctionProductBid::where('product_id', $product->product_id)->where('user_id', $user->id)->first();
        $oldBid = AuctionProductBid::where('product_id', $product->product_id)
        ->orderBy('amount', 'desc')
        ->first();
        $bidAmount = $oldBid->amount + 0.1;

        if($bidByBot != null){

            $bidByBot->amount = $bidAmount;
            $bidByBot->save();
            return;
        }
        
        // dd("execution is in else block");

        $bid =  new AuctionProductBid;
        $bid->user_id = $user->id;
        $bid->amount = $bidAmount;
        $bid->product_id = $product->product_id;
        $bid->save();

        
        // if($bidByBot != null){
        //     if($oldBid){
        //         $oldBid->amount = $oldBid->amount + 0.1;
        //     }else{
        //         $oldBid->amount =  0.1;
        //     }
        //     $oldBid->save();
        //     return;
            
        // }else{
        //     $bid =  new AuctionProductBid;
        //     $bid->user_id = $user->id;
        //     if($oldBid != null){
        //         $bid->amount = $oldBid->amount + 0.1;
        //     }else{
        //         $bid->amount =  0.1;
        //     }
        // }
        
        // $bid->product_id = $product->product_id;
        // $bid->save();
        
        


        
    }





}