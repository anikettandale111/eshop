<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/register', [\App\Http\Controllers\Api\APIController::class, 'register']);
Route::post('/verifyotp', [\App\Http\Controllers\Api\APIController::class, 'verifyOTP']);
Route::post('/resendotp', [\App\Http\Controllers\Api\APIController::class, 'resendOTP']);
Route::post('/login', [\App\Http\Controllers\Api\APIController::class, 'login']);
// Route::group(['middleware' => ['api']], function () {
// });

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/testcallapi', [\App\Http\Controllers\Api\APIController::class, 'testApiCall']);
    Route::get('banners', [\App\Http\Controllers\Api\APIController::class, 'listBanners']);
    // Profile UPdate
    Route::post('/profileupdate', [\App\Http\Controllers\Api\APIController::class, 'profileUpdate']);
    // category Listing
    Route::get('categories', [\App\Http\Controllers\Api\APIController::class, 'categories']);
    // product Listing
    Route::get('products', [\App\Http\Controllers\Api\APIController::class, 'products']);
    // Address Listing
    Route::get('address', [\App\Http\Controllers\Api\APIController::class, 'listAddress']);
    // Address add OR update 
    Route::post('addupdateadd', [\App\Http\Controllers\Api\APIController::class, 'addUpdateaddress']);
    // Product By Category
    Route::get('category/{catid}', [\App\Http\Controllers\Api\APIController::class, 'productByCategory']);
    // Product Details
    Route::get('product-detail/{pslug}/', [\App\Http\Controllers\Api\APIController::class, 'productDetail']);
    // Get Cart Details
    Route::get('cart', [\App\Http\Controllers\Api\APIController::class, 'cart']);
    // Get Cart Details
    Route::post('single/cart/store', [\App\Http\Controllers\Api\APIController::class, 'singleCartStore']);
    // Delete Cart
    Route::get('cart/delete/{id?}/{var?}', [\App\Http\Controllers\Api\APIController::class, 'cartDelete']);
    // Update Cart
    Route::post('cart/update', [\App\Http\Controllers\Api\APIController::class, 'cartUpdate']);
    // Add to Cart
    Route::post('cart/add', [\App\Http\Controllers\Api\APIController::class, 'cartAdd']);
    // Cart Duplicate Check
    Route::post('cart/duplicate', [\App\Http\Controllers\Api\APIController::class, 'checkDuplicateCart']);
    // Checkout
    Route::post('checkout', [\App\Http\Controllers\Api\APIController::class, 'checkoutStore']);
});

//product review
Route::post('user/review', [\App\Http\Controllers\Api\ProductController::class, 'reviewSubmit']);
//Userorder
Route::get('user/orders', [\App\Http\Controllers\Api\UserController::class, 'userOrder']);
//user order detail
Route::get('user/order/{orderId}', [\App\Http\Controllers\Api\UserController::class, 'userOrderDetail']);
// Route::post('register',[\App\Http\Controllers\Api\AuthController::class,'register']);
// Route::post('login',[\App\Http\Controllers\Api\AuthController::class,'login']);

// BannerApi
Route::apiResource('/banner', \App\Http\Controllers\Api\BannerController::class)->middleware('auth:api');

// BrandApi
Route::get('/brands', [\App\Http\Controllers\Api\FrontendController::class, 'brands']);

//CategoryApi

//featured category
Route::get('featured-category', [\App\Http\Controllers\Api\CategoryController::class, 'featuredCategory']);

//couponApi
Route::get('coupons', [\App\Http\Controllers\Api\FrontendController::class, 'coupons']);

//orderApi
Route::get('orders', [\App\Http\Controllers\Api\FrontendController::class, 'orders']);

//Deal of the day
Route::get('deal_of_the_day', [\App\Http\Controllers\Api\ProductController::class, 'dealOfTheDay']);
// featured products
Route::get('featured-products', [\App\Http\Controllers\Api\ProductController::class, 'featuredProducts']);
// new products
Route::get('new-products', [\App\Http\Controllers\Api\ProductController::class, 'newProducts']);
// hot products
Route::get('hot-products', [\App\Http\Controllers\Api\ProductController::class, 'hotProducts']);

//search product
Route::get('search-product', [\App\Http\Controllers\Api\ProductController::class, 'searchProduct']);

//product detail
Route::get('product-detail', [\App\Http\Controllers\Api\ProductController::class, 'productDetail']);

// cartApi section
Route::get('user/get-cart', [\App\Http\Controllers\Api\CartController::class, 'userGetCart']);
Route::post('user/add-to-cart', [\App\Http\Controllers\Api\CartController::class, 'addToCart']);

//settings
Route::get('settings', [\App\Http\Controllers\Api\FrontendController::class, 'settings']);
