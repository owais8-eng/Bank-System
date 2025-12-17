<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\transactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


Route::middleware('auth:sanctum')->prefix('accounts')->group(function () {

    Route::get('/', [AccountController::class, 'index']);
    Route::get('/{account}', [AccountController::class, 'show']);
    Route::post('/', [AccountController::class, 'store']);
    Route::put('/{account}', [AccountController::class, 'update']);
    Route::patch('/{account}/state', [AccountController::class, 'changeState']);
    Route::post('/{account}/close', [AccountController::class, 'close']);
});

Route::middleware('auth:sanctum')
    ->prefix('customer')
    ->group(function () {
        Route::get('/accounts', [CustomerAccountController::class, 'index']);
        Route::get('/accounts/{account}', [CustomerAccountController::class, 'show']);
        Route::put('/accounts/{account}', [CustomerAccountController::class, 'update']);
    });

Route::middleware('auth:sanctum')
    ->prefix('admin')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index']);
        Route::get('/reports', [ReportController::class, 'generate']);
    });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/accounts/{account}/deposit', [transactionController::class, 'deposit']);
    Route::post('/accounts/{account}/withdraw', [TransactionController::class, 'withdraw']);
    Route::post('/accounts/{account}/transfer', [TransactionController::class, 'transfer']);
});

Route::middleware('auth:sanctum')->post('/recurring-transactions',[RecurringTransactionController::class, 'store']);
