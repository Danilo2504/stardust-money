<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use Illuminate\Support\Facades\Route;

// Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('pages.dashboard');
    })->name('dashboard');
    
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::resource('expenses', ExpenseController::class)
        ->except(['create', 'edit']);
    Route::patch('expenses/{expense}/confirm', [ExpenseController::class, 'confirm'])
        ->name('expenses.confirm');
// });