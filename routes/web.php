<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthWithUnkey;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/public', [ApiController::class, 'publicRoute']);
Route::get('/protected', [ApiController::class, 'protectedRoute'])
    ->middleware([AuthWithUnkey::class . ':withAuth']);

