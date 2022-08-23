<?php

use App\Http\Controllers\CheckSessionController;
use App\Http\Controllers\EditUserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\PrestashopController;
use App\Http\Controllers\PcikingController;
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

Route::post('/users/register', RegisterController::class)->name('register');
Route::post('/users/login', LoginController::class)->name('login');
Route::post('/users/logout', LogoutController::class)->name('logout');
Route::post('/users/checkSession', CheckSessionController::class)->name('check-session');
Route::any('/users/edit', EditUserController::class)->name('edit-user');

Route::get('/orders/fetch_prestashop_orders', 'App\Http\Controllers\PrestashopController@get_orders')->name('fetch-orders');
Route::get('/orders/get_list', 'App\Http\Controllers\PrestashopController@get_order_list')->name('get-orders');
Route::get('/orders/get_picked_list', 'App\Http\Controllers\PrestashopController@get_orders_picked_list')->name('get-orders');


Route::get('/orders/paginate', 'App\Http\Controllers\PrestashopController@pagination')->name('pagination');
Route::get('/orders/{reference}', 'App\Http\Controllers\PrestashopController@get_products_by_order')->name('update-order');


Route::get('/get_product_images', 'App\Http\Controllers\PrestashopController@get_products_images')->name('product-images');


Route::get('/carriers/fetch_prestashop_carriers', 'App\Http\Controllers\PrestashopController@get_carriers')->name('fetch-carrierss');

Route::get('/customers', 'App\Http\Controllers\PrestashopController@get_customers')->name('get-customers');
Route::get('/products', 'App\Http\Controllers\PrestashopController@get_products')->name('get-products');

Route::post('/orders/get_list', 'App\Http\Controllers\PrestashopController@paginate')->name('paginate-orders');
Route::get('/picking/{reference}/{sku}', 'App\Http\Controllers\PrestashopController@product_data');


Route::get('/picking/picking_info/{reference}/{sku}', 'App\Http\Controllers\PickingController@get_picking');
Route::post('/picking/save_picking', 'App\Http\Controllers\PickingController@save_picking');


Route::middleware(['auth:api'])->group(function () {
    //
});
