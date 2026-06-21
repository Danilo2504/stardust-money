<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('expenses/data', [ExpenseController::class, 'data'])
        ->name('expenses.data');
    Route::patch('expenses/{expense}/confirm', [ExpenseController::class, 'confirm'])
        ->name('expenses.confirm');
    Route::resource('expenses', ExpenseController::class)
        ->except(['create', 'edit']);
});

require __DIR__.'/auth.php';
