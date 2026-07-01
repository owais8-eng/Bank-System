<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WarehouseDemoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/warehouse-demo', [WarehouseDemoController::class, 'index']);
