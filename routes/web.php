<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/invoice/extract-data', [InvoiceController::class, 'extractData']);
Route::get('/invoice', [InvoiceController::class, 'index']);
Route::get('/invoice/create/{doId}', [InvoiceController::class, 'create']);
Route::post('/invoice/store', [InvoiceController::class, 'store']);
Route::post('/audit/log', [InvoiceController::class, 'clientAuditLog']);
Route::get('/invoice/do-items/{doId}', [InvoiceController::class, 'getDoItems']);
require __DIR__.'/auth.php';
