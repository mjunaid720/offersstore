<?php

use Illuminate\Http\Request;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */


Route::group(['middleware' => ['auth:api']], function () {
//store routes start

    Route::post('create-store', 'API\StoreController@createStore');
    Route::get('store-edit/{id}', 'API\StoreController@getStoreDataById');
    
    Route::post('store-update', 'API\StoreController@updateStore');
    
    //store routes end
    //
    //
    //
    //
   // parms flyer_title ,flyer_image
    Route::post('add-flyer', 'API\FlyerController@createFlyer');
    //offer routes start
    Route::post('create-offer', 'API\OfferController@createOffer');
    Route::get('offer-edit/{id}', 'API\OfferController@getOfferById');
    Route::post('offer-update', 'API\OfferController@updateOffer');
    Route::get('getoffers', 'API\OfferController@getAllOffers');
    Route::post('getstoreoffers', 'API\OfferController@getstoreOffers');
    Route::post('deleteoffers', 'API\OfferController@deleteOffers');
    Route::get('getstores', 'API\StoreController@getStores');
    //parm store_id
    Route::post('store-delete', 'API\StoreController@storeDelete');
    
    //user routes
    //
    //parms user id
    Route::get('account-information', 'API\RegisterController@getUserDetail');
   // parms name email phone
     Route::post('update-user', 'API\RegisterController@updateUserDetail');
     //password c_password
     Route::post('change-password', 'API\RegisterController@changeUserPassword');
    
    // book mark routes
    // 
     //parms offer_id
      Route::post('add-bookmark', 'API\BookmarkController@addBookmark');
      Route::get('bookmarks', 'API\BookmarkController@getUserBookmarks');
     // parms offer_id
      Route::post('delete-bookmark', 'API\BookmarkController@deleteBookmark');
     
     
});
//parms store_id
    Route::post('store-detail', 'API\StoreController@getStoreDataById');
Route::get('getstores-home', 'API\StoreController@getStores');
Route::get('gettrends', 'API\OfferController@getTrendOffers');
// get categories for home page and also get their offer count and first three categories in it

Route::get('getcategoryoffer', 'API\OfferController@getAllCategoriesByNoOffer');
//store routes start
Route::get('countries', 'API\StoreController@getStoreCountries');
Route::get('states/{id}', 'API\StoreController@getStatesByCountryId');

//offers route start
Route::post('product-categories', 'API\OfferController@getproductsByCatId');
//parm offer_id
Route::post('offer-detail', 'API\OfferController@getOfferDetail');
//offers route end

Route::get('categories', 'API\OfferController@getAllCategories');
Route::get('home-categories', 'API\OfferController@getHomeCategories');
Route::get('child-categories/{id}', 'API\OfferController@getChildCategories');
Route::get('asif/{id}', 'API\OfferController@getTotaloffers');
//categories route end
//
//    
//
//users routes start
Route::post('login', 'API\RegisterController@login');
Route::post('register', 'API\RegisterController@register');
//users routes end

//flyer route start
Route::get('flyers', 'API\FlyerController@getAllFlyers');



Route::get('getuser', 'API\RegisterController@getDetails')->middleware('scopes:user');
Route::resource('products', 'API\ProductController');
