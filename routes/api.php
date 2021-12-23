<?php

use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WalletController;
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

Route::middleware('auth:passport')->get('/user', function (Request $request) {
    return $request->user();
});


//To view customer's wallet
Route::get('customer/{id}/wallet', [WalletController::class, 'viewWallet']);

//To create the customer's wallet
Route::post('customer/{id}/wallet/create', [WalletController::class, 'createWallet']);

//To fund the customer's wallet
Route::post('customer/{id}/wallet/fund', [WalletController::class, 'fundWallet']);

//To verify transaction
Route::get('customer/{id}/wallet/verify', [WalletController::class, 'verifyTransaction']);

//To view all customer's transactions
Route::get('customer/{id}/wallet/transaction', [TransactionController::class, 'view']);
