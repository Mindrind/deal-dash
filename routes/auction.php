<?php

/*
|--------------------------------------------------------------------------
| Auction Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Admin
Route::group(['prefix' =>'admin', 'middleware' => ['auth', 'admin']], function(){
    Route::resource('auction_products', 'AuctionProductController');
    Route::post('/auction_products/update/{id}', 'AuctionProductController@update')->name('auction_products.update');
    Route::get('/auction_products/admin/edit/{id}', 'AuctionProductController@admin_product_edit')->name('auction_products.admin.edit');
    Route::get('/auction_products/destroy/{id}', 'AuctionProductController@destroy')->name('auction_products.destroy');

  //  Route::resource('product_bids', 'AuctionProductBidController');
   // Route::get('/product_bids/destroy/{id}', 'AuctionProductBidController@destroy')->name('product_bids.destroy');

    // Sales
  //  Route::get('/auction_products-orders', 'AuctionProductController@admin_auction_product_orders')->name('auction_products_orders');
  //  Route::get('/auction_orders/{id}/show', 'AuctionProductController@auction_orders_show')->name('auction_orders.show');
});

Route::group(['middleware' => ['auth']], function() {
   // Route::resource('auction_product_bids', 'AuctionProductBidController');
    Route::post('/auction/cart/show-cart-modal', 'CartController@showCartModalAuction')->name('auction.cart.showCartModal');
    Route::get('/auction_products/purchase_history', 'AuctionProductController@purchase_history_user')->name('auction_product.purchase_history');
});

//Route::post('/home/section/auction_products', 'HomeController@load_auction_products_section')->name('home.section.auction_products');
Route::get('/auction-product/{slug}', 'AuctionProductController@auction_product_details')->name('auction-product');
Route::get('/auction-products', 'AuctionProductController@all_auction_products')->name('auction_products.all');
