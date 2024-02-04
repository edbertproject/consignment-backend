<?php

use App\Entities\Order;
use App\Entities\User;
use App\Notifications\PartnerSellerNewOrderNotification;
use App\Notifications\TestEmailNotification;
use App\Utils\Constants;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

Auth::routes(['register' => false]);
// Route::view( '/', 'mail.invoice.success-payment');
Route::view('/home', 'home')->middleware('auth')->name('home');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/scheduler', [\App\Http\Controllers\SchedulersController::class, 'artisanScheduler']);
