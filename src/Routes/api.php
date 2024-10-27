<?php

use Illuminate\Support\Facades\Route;
use Iransh\Wallet\Controllers\WalletController;
use Iransh\Wallet\Models\Blockchain;

Route::middleware('auth:sanctum')->prefix('/api')->group(function () {
    Route::get('/wallet/balance', [WalletController::class, 'balance']);
    Route::post('/wallet/deposit', [WalletController::class, 'deposit']);
    Route::post('/wallet/withdraw', [WalletController::class, 'withdraw']);
    Route::post('/wallet/transfer', [WalletController::class, 'transfer']); 
});


Route::get('/blockchain/validate', function () {
    $isValid = Blockchain::isChainValid();
    return response()->json(['valid' => $isValid]);
});