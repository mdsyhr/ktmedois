<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Models\Supplier;

Route::get('/', function () {
    return view('welcome');
});

// Vendor Dashboard
Route::get('/vendor/dashboard', function () {
    $supplier = Supplier::where('User_ID', auth()->user()->User_ID)->first();
    return view('vendordashboard', compact('supplier'));
})->middleware(['auth'])->name('vendor.dashboard');

// Default dashboard
Route::get('/dashboard', function () {
    $supplier = Supplier::where('User_ID', auth()->user()->User_ID)->first();
    return view('vendordashboard', compact('supplier'));
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';