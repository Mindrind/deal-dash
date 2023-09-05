<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
Use App\Models\AuctionProductBid;
use App\Models\User;
use App\Models\BotUser;
Use App\Models\Product;
use Auth;
use DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Faker\Factory as Faker;
use Artisan;
use Illuminate\Support\Facades\Http;

class AuctionProductBidController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $bids = DB::table('auction_product_bids')
            ->orderBy('id', 'desc')
            ->join('products', 'auction_product_bids.product_id', '=', 'products.id')
            ->where('auction_product_bids.user_id',Auth::user()->id)
            ->select('auction_product_bids.id')
            ->distinct()
            ->paginate(10);
        return view('auction.frontend.my_bidded_products', compact('bids'));
    }

    // public function bidingByBots2()
    // {
        


    //     Artisan::call('cache:clear');
    //     Artisan::call('route:clear');
    //     response()->json(["status" => "success"], 200);


    //     $auctionProducts = AuctionProductBid::all();
        
    //     $products = $this->filterExpiredBids($auctionProducts);
    //     foreach($products as $product){
    //         $this->placeBidByBot($product);
    //     }

    //     sleep(rand(1, 10));


    //     Http::get(url('/bidingByBots'));
    //     exit();



        
    // }







public function bidingByBots2()
{

    Artisan::call('cache:clear');
    Artisan::call('route:clear');


    $currentTs =time();
    // get all boots
    $systembots = DB::select( DB::raw("SELECT * FROM users WHERE is_bot_user = 1") ); // return array of boots objects


    // get All auction products
    $auction_products = DB::select(
        DB::raw(
            "SELECT * FROM products WHERE auction_product = 1 AND auction_start_date <=  ".$currentTs." AND   auction_end_date >= ".$currentTs)
    ); // return array of products objects
    // auction_start_date auction_end_date Ts   // reserved_price unit_price
    // WHERE DT IS BETWEEN auction_start_date and  auction_end_date


    if(!empty($auction_products) && !empty($systembots)){

        // Loop through boots
        foreach ($systembots as $systembot){

            $boots_ids = array_column($systembots, 'id');
            if(!empty($boots_ids)){
                $boots_ids = implode( ',', $boots_ids );
            }

            
            foreach ( $auction_products as $auction_product ){
            
                 $boot_bid_exist = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE user_id = ".$systembot->id) );
                 if(empty($boot_bid_exist)){
                     
                    $bid_exist = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id) );
                    //case one
                    if( empty($bid_exist) ){
    
                        $bidAmount = $auction_product->starting_bid + 0.10;
                        $this->placeBidByBotAvi($auction_product,$systembot,$bidAmount);
                        // bid not exist than just bid
                       // dd($bid_exist);
                    }
                    // case two bid exist
                    else{
                        // now exist bid scnerios
    
                        // 1st check that this boot aleady bit on that product or not
                        $boot_bidded = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." AND user_id =".$systembot->id) );
    
                        if(!empty($boot_bidded)){
                                 $latest_boot_bd = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." AND  user_id IN (".$boots_ids.") ORDER BY auction_product_bids.amount DESC LIMIT 1" ) );
                                    if(!empty($latest_boot_bd) && $auction_product->reserved_price < ( $latest_boot_bd[0]->amount + 0.10 ) ){
                                        // good dont bid
                                        continue;
                                    }else{
                                        // Time to bid with this boot
                                       // dd($auction_product->reserved_price,$latest_boot_bd[0]->amount + 0.1);
                                      
                                       if(!empty($latest_boot_bd)){
                                           
                                        $bidAmount = $latest_boot_bd[0]->amount + 0.10;
                                        $this->placeBidByBotAvi($auction_product,$systembot,$bidAmount);
                                        // sleep(rand(1, 10));
                                       }
                                    }
                            
                            //already bidded next boot baby continue
                           // continue;
                        }
                        else{
                            
                            // Not Bidded Yet
    
                            // 2nd if not than check latest user bid is > than reserved price or not
                            $max_bid = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." ORDER BY auction_product_bids.amount DESC LIMIT 1") );
    
    
                            if(!empty($max_bid) &&  $max_bid[0]->amount > $auction_product->reserved_price ){
                                continue;
                                // good dont bid
                            }else{
                                // 3rd number id boots bid is <7
                                $boots_bid_count = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id) );
                                if(!empty($boots_bid_count) && count($boots_bid_count) >= 7){
                                    continue;
                                    // good dont bid
                                }else{
                                    // 4th than check latest boot bit + 1c is < reserved bit or not
                                    // get last boot bit
    
                                    $latest_boot_bd = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." AND  user_id IN (".$boots_ids.") ORDER BY auction_product_bids.amount DESC LIMIT 1" ) );
                                    if(!empty($latest_boot_bd) && $auction_product->reserved_price < ( $latest_boot_bd[0]->amount + 0.10 ) ){
                                        // good dont bid
                                        continue;
                                    }else{
                                        // Time to bid with this boot
                                       // dd($auction_product->reserved_price,$latest_boot_bd[0]->amount + 0.10);
                                      
                                       if(!empty($latest_boot_bd)){
                                           
                                        $bidAmount = $latest_boot_bd[0]->amount + 0.10;
                                        $this->placeBidByBotAvi($auction_product,$systembot,$bidAmount);
                                        // sleep(rand(1, 10));
                                       }
                                    }
    
                                }
    
                              //  dd($max_bid,'Max bid',$max_bid[0]->amount,$auction_product->reserved_price);
    
                            }
    
                        }
                    
                        
                    }
                 }else{
                     if(
                         !empty($boot_bid_exist)
                         )
                     {
                         if($boot_bid_exist[0]->product_id == $auction_product->id ){
                                          //case one
                    if( empty($bid_exist) ){
    
                        $bidAmount = $auction_product->starting_bid + 0.10;
                        $this->placeBidByBotAvi($auction_product,$systembot,$bidAmount);
                        // bid not exist than just bid
                       // dd($bid_exist);
                    }
                    // case two bid exist
                    else{
                        // now exist bid scnerios
    
                        // 1st check that this boot aleady bit on that product or not
                        $boot_bidded = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." AND user_id =".$systembot->id) );
    
                        if(!empty($boot_bidded)){
                                 $latest_boot_bd = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." AND  user_id IN (".$boots_ids.") ORDER BY auction_product_bids.amount DESC LIMIT 1" ) );
                                    if(!empty($latest_boot_bd) && $auction_product->reserved_price < ( $latest_boot_bd[0]->amount + 0.10 ) ){
                                        // good dont bid
                                        continue;
                                    }else{
                                        // Time to bid with this boot
                                       // dd($auction_product->reserved_price,$latest_boot_bd[0]->amount + 0.10);
                                      
                                       if(!empty($latest_boot_bd)){
                                           
                                        $bidAmount = $latest_boot_bd[0]->amount + 0.10;
                                        $this->placeBidByBotAvi($auction_product,$systembot,$bidAmount);
                                        // sleep(rand(1, 10));
                                       }
                                    }
                            
                            //already bidded next boot baby continue
                           // continue;
                        }
                        else{
                            
                            // Not Bidded Yet
    
                            // 2nd if not than check latest user bid is > than reserved price or not
                            $max_bid = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." ORDER BY auction_product_bids.amount DESC LIMIT 1") );
    
    
                            if(!empty($max_bid) &&  $max_bid[0]->amount > $auction_product->reserved_price ){
                                continue;
                                // good dont bid
                            }else{
                                // 3rd number id boots bid is <7
                                $boots_bid_count = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id) );
                                if(!empty($boots_bid_count) && count($boots_bid_count) >= 7){
                                    continue;
                                    // good dont bid
                                }else{
                                    // 4th than check latest boot bit + 1c is < reserved bit or not
                                    // get last boot bit
    
                                    $latest_boot_bd = DB::select( DB::raw("SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." AND  user_id IN (".$boots_ids.") ORDER BY auction_product_bids.amount DESC LIMIT 1" ) );
                                    if(!empty($latest_boot_bd) && $auction_product->reserved_price < ( $latest_boot_bd[0]->amount + 0.10 ) ){
                                        // good dont bid
                                        continue;
                                    }else{
                                        // Time to bid with this boot
                                       // dd($auction_product->reserved_price,$latest_boot_bd[0]->amount + 0.10);
                                      
                                       if(!empty($latest_boot_bd)){
                                           
                                        $bidAmount = $latest_boot_bd[0]->amount + 0.10;
                                        $this->placeBidByBotAvi($auction_product,$systembot,$bidAmount);
                                        // sleep(rand(1, 10));
                                       }
                                    }
    
                                }
    
                              //  dd($max_bid,'Max bid',$max_bid[0]->amount,$auction_product->reserved_price);
    
                            }
    
                        }
                    
                        
                    }
                         }
                     }
                 }

                // dd($systembot->id,$auction_product->id);

            }
        }
        response()->json(["status" => "success"], 200);
    }else{
        response()->json(["status" => "success",'message' =>'Not Data Found'], 200);
    }

    response()->json(["status" => "success"], 200);

//    // Fork a child process to run the while loop
//    $pid = pcntl_fork();
//
//    if ($pid == -1) {
//        // Error forking process
//        die('Error forking process.');
//    } else if ($pid) {
//
//        // Parent process - continue with other tasks
//        return response()->json(["status" => "success"], 200);
//    } else {
//
//        dd('get to work code here');
//
//        // Child process - run the while loop
//        // $continueLoop = true;
//        // while ($continueLoop) {
//            $auctionProducts = AuctionProductBid::all();
//
//            $products = $this->filterExpiredBids($auctionProducts);
//            foreach($products as $product){
//                $this->placeBidByBot($product);
//            }
//            sleep(rand(1, 10));
//            // while false
//        // }
//        exit(); // Child process should exit after finishing its task
//    }

}

public function winned_products(){
    
    
    $currentTs =time();
    // get all boots
    $systembots = DB::select( DB::raw("SELECT * FROM users WHERE is_bot_user = 1") ); // return array of boots objects


    // get All auction products
    $auction_products = DB::select(
        DB::raw(
            "SELECT * FROM products WHERE auction_product = 1  AND auction_winned = 0 AND  auction_end_date <= ".$currentTs)
    );
    
     $boots_ids = array_column($systembots, 'id');
            if(!empty($boots_ids)){
                $boots_ids = implode( ',', $boots_ids );
            }
            
            foreach($auction_products as $auction_product){
                
                    $latest_user_bd = DB::select( DB::raw("
                        SELECT * FROM auction_product_bids WHERE product_id = ".$auction_product->id." AND  user_id NOT IN (".$boots_ids.")
                        ORDER BY auction_product_bids.amount DESC LIMIT 1" ) );
                       
                        if(!empty($latest_user_bd)){
                               
                               $affected = DB::table('products')
                                  ->where('id', $auction_product->id)
                                  ->update([
                                      'auction_winned' => 1,
                                      'auction_winned_by_user' => $latest_user_bd[0]->user_id,
                                      'auction_winned_amount' => $latest_user_bd[0]->amount,
                                  ]);
                        }

                        
                     //   DB::statement("UPDATE products  SET auction_winned = 1 ,auction_winned_by_user = 2 where id =1");
                     //   UPDATE products SET auction_winned='' , auction_winned_by_user=''  WHERE id =
                
            }

    
}




/**
 * Show the form for creating a new resource.
 *
 * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $bid = AuctionProductBid::where('product_id',$request->product_id)->where('user_id',Auth::user()->id)->first();

        
        $this->reduceAmountFromUserVirtualWallet($request->amount, $bid);

        
        if($bid == null){
            $bid =  new AuctionProductBid;
            $bid->user_id = Auth::user()->id;
        }
        $bid->product_id = $request->product_id;
        $bid->amount = $request->amount;
        if($bid->save()){
            
            
            return redirect()->back();

            // flash(translate('Bid Placed Successfully'))->success();
        }
        else{
            flash(translate('Something went wrong!'))->error();
        }
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::where('id',$id)->first();
        $bids = AuctionProductBid::latest()->where('product_id', $id)->paginate(15);
        return view('auction.auction_products.bids', compact('bids','product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        AuctionProductBid::destroy($id);
        flash(translate('Bid deleted successfully'))->success();
        return back();
    }

    private function isAuctionExpired($product) {


        if(!$product OR !$product->product){
            return true;
        }


        $currentTime = time(); // Get the current Unix timestamp
        $auctionTime = $product->product->auction_end_date; // Convert the date string to a Unix timestamp
        if ($auctionTime < $currentTime) {
            return true; // The auction has expired
        } else {
            return false; // The auction is still active
        }
    }


    // public function placeBidByBot ($product) {
    //     $user = $this->randomBotUser();

    //     $bidByBot = AuctionProductBid::where('product_id', $product->product_id)->where('user_id', $user->id)->first();
        
        
    //     $oldBid = AuctionProductBid::where('product_id', $product->product_id)
    //     ->orderBy('amount', 'desc')
    //     ->first();
    
    //     $bidAmount = $oldBid->amount + 0.1;
    
    //     // if bid amount is equal or greater than reserved price then stopping the bots to bids;
    //     if($bidAmount > $product->product->reserved_price) { return; }

   

    //     if($bidByBot != null){

    //         $bidByBot->amount = $bidAmount;
    //         $bidByBot->save();
    //         return;
    //     }
    //     $bid =  new AuctionProductBid;
    //     $bid->user_id = $user->id;
    //     $bid->amount = $bidAmount;
    //     $bid->product_id = $product->product_id;
    //     $bid->save();
    // }



    public function placeBidByBot ($product) {
        $user = $this->randomBotUser();

        $bidByBot = AuctionProductBid::where('product_id', $product->product_id)->where('user_id', $user->id)->first();
        
        
        $oldBid = AuctionProductBid::where('product_id', $product->product_id)
        ->orderBy('amount', 'desc')
        ->first();
    
        $bidAmount = $oldBid->amount + 0.1;
    
        // if bid amount is equal or greater than reserved price then stopping the bots to bids;
        if($bidAmount > $product->product->reserved_price) { return; }

   

        if($bidByBot != null){

            $bidByBot->amount = $bidAmount;
            $bidByBot->save();
            return;
        }
        $bid =  new AuctionProductBid;
        $bid->user_id = $user->id;
        $bid->amount = $bidAmount;
        $bid->product_id = $product->product_id;
        $bid->save();
    }


    public function placeBidByBotAvi ($product,$user,$bidAmount) {

        $bid =  new AuctionProductBid;
        $bid->user_id = $user->id;
        $bid->amount = $bidAmount;
        $bid->product_id = $product->id;
        $bid->save();
    }







    public function randomBotUser()
    {
        // Get a random user where is_bot_user is equal to 1
        $randomBotUser = User::where('is_bot_user', 1)->inRandomOrder()->first();

        // in case if bot user not found then we are creating first and then returning it;
        if($randomBotUser == null){
            $faker = Faker::create();

            $randomBotUser = User::create([
                'name' => $faker->name, 
                'email' => $faker->email,
                'location' => $faker->country,
                'is_bot_user' => 1 
            ]);
            return $randomBotUser;
        }
        return $randomBotUser;
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


    private function reduceAmountFromUserVirtualWallet($amount, $bid){
        $user = auth()->user();
        $userWallet = $user->virtual_wallet;

        if($bid == null ){

            $userWallet->balance = $userWallet->balance - $amount;
            $userWallet->save();
        }else{


            $userWallet->balance = $userWallet->balance - $amount + $bid->amount;
            $userWallet->save();

        }

        
    }


}
