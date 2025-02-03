<?php

use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/task', [App\Console\Commands\RecurringPayment::class, 'handle'])->name('handle');


Route::get('/', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('home_login');
Route::get('/admin', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('admin');
Route::post('/adminpost', [App\Http\Controllers\Auth\LoginController::class, 'admin'])->name('admin_post');



Route::group(['middleware' => ['auth']], function () {
    Route::get('/negotiators/list', [DashboardController::class, 'negotiators_list'])->name('negotiators.list');

    // Review
    Route::get('/reviews', [DashboardController::class, 'reviews'])->name('reviews');
    Route::delete('/reviews/delete/{id}', [DashboardController::class, 'reviewsDel'])->name('reviews.destroy');

    // Quote
    Route::get('/quotes', [DashboardController::class, 'quotes'])->name('quotes');
    Route::delete('/quotes/delete/{id}', [DashboardController::class, 'quotesDel'])->name('quotes.destroy');

    // Bid
    Route::get('/bids', [DashboardController::class, 'bids'])->name('bids');
    Route::delete('/bids/delete/{id}', [DashboardController::class, 'bidsDel'])->name('bids.destroy');

    // User
    Route::get('/users', [DashboardController::class, 'users'])->name('users');
    Route::delete('/users/delete/{id}', [DashboardController::class, 'usersDel'])->name('users.destroy');



    Route::get('/helper', [App\Http\Controllers\NotificationController::class, 'helper']);
    Route::resource('/notification',App\Http\Controllers\NotificationController::class);
    Route::resource('/orders',App\Http\Controllers\OrdersController::class);
 	// Route::resource('trophy',App\Http\Controllers\TrophyController::class);
    Route::get('/home', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('home');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/admin_info', [App\Http\Controllers\DashboardController::class, 'admin_info'])->name('admin_info');
    Route::post('/admin_info_post', [App\Http\Controllers\DashboardController::class, 'admin_info_post'])->name('admin_info_post');
    Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
    Route::resource('guides',App\Http\Controllers\GuideController::class);
    Route::get('/terms/conditions', [App\Http\Controllers\TermAndConditionController::class, 'index'])->name('terms_conditions');
    Route::post('/terms-conditions', [App\Http\Controllers\TermAndConditionController::class, 'termandcontionpost'])->name('terms_conditions_post');
    Route::get('/transaction', [App\Http\Controllers\TranasactionController::class, 'transaction_list'])->name('transaction');
    Route::get('/transaction_status/{id}', [App\Http\Controllers\TranasactionController::class, 'transaction_status'])->name('transaction_status');

    Route::resource('category',App\Http\Controllers\CategoryController::class);
    Route::resource('brand',App\Http\Controllers\BrandController::class);
    Route::resource('product',App\Http\Controllers\ProductController::class);
    Route::resource('shipping',App\Http\Controllers\ShippingController::class);

    // Notification
    // Route::get('/notification/{id}',[\App\Http\Controllers\NotificationController::class,'show'])->name('admin.notification');

});
