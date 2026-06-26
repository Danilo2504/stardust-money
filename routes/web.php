<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InstallmentGroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecurringExpenseController;
use App\Http\Controllers\SharedReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('/share/{token}', [SharedReportController::class, 'publicShow'])
    ->name('shared-reports.public');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('categories/data', [CategoryController::class, 'data'])
        ->name('categories.data');
    Route::get('categories/select', [CategoryController::class, 'select'])
        ->name('categories.select');
    Route::resource('categories', CategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('expenses/data', [ExpenseController::class, 'data'])
        ->name('expenses.data');
    Route::patch('expenses/{expense}/confirm', [ExpenseController::class, 'confirm'])
        ->name('expenses.confirm');
    Route::resource('expenses', ExpenseController::class)
        ->except(['create', 'edit']);

    Route::get('recurring-expenses/data', [RecurringExpenseController::class, 'data'])
        ->name('recurring-expenses.data');
    Route::resource('recurring-expenses', RecurringExpenseController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('installment-groups/data', [InstallmentGroupController::class, 'data'])
        ->name('installment-groups.data');
    Route::resource('installment-groups', InstallmentGroupController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('shared-reports/data', [SharedReportController::class, 'data'])
        ->name('shared-reports.data');
    Route::resource('shared-reports', SharedReportController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});

require __DIR__.'/auth.php';
