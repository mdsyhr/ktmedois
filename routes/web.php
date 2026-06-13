<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;
use App\Models\Supplier;

Route::get('/', function () {
    return redirect()->route('login');
});

// Vendor Dashboard
Route::get('/vendor/dashboard', function () {
    $supplier = Supplier::where('User_ID', auth()->user()->User_ID)->first();
    return view('usermanagement.vendor.Vendor_DashboardView', compact('supplier'));
})->middleware(['auth'])->name('vendor.dashboard');

// Default dashboard
Route::get('/dashboard', function () {
    $supplier = Supplier::where('User_ID', auth()->user()->User_ID)->first();
    return view('usermanagement.vendor.Vendor_DashboardView', compact('supplier'));
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

require __DIR__.'/auth.php';
