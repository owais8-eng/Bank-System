<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountInterestController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\RecurringTransactionController;
use App\Http\Controllers\TicketController;
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
    Route::post('/accounts/transfer', [TransactionController::class, 'transfer']);
    Route::get('transactionHistory/{account}',[transactionController::class,'transactionHistory']);
    Route::get('auditLog/{account}',[transactionController::class,'auditLog']);
});

Route::middleware('auth:sanctum')->prefix('notifications')->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'unread']);
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::patch('/read-all', [NotificationController::class, 'markAllAsRead']);
});


Route::middleware('auth:sanctum')->prefix('tickets')->group(function () {
    Route::get('/', [TicketController::class, 'index']);
    Route::post('/', [TicketController::class, 'store']);
    Route::get('/{ticket}', [TicketController::class, 'show']);
    Route::post('/{ticket}/comment', [TicketController::class, 'addComment']);
    Route::patch('/{ticket}/status', [TicketController::class, 'updateStatus']);
});

Route::middleware('auth:sanctum')
    ->get('/recommendations', [RecommendationController::class, 'recommendations']);


Route::get('/accounts/{account}/interest', [AccountInterestController::class, 'calculate']);

Route::middleware('auth:sanctum')->post('/recurring-transactions',[transactionController::class, 'scheduleTransaction']);
