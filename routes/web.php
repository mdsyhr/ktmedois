<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DeliveryOrderController;
use Illuminate\Support\Facades\Route;
use App\Models\Supplier;
use Illuminate\Support\Facades\Mail;


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
Route::post('/invoice/extract-data', [InvoiceController::class, 'extractData']);
Route::get('/invoice', [InvoiceController::class, 'index']);
Route::get('/invoice/create/{doId}', [InvoiceController::class, 'create']);
Route::post('/invoice/store', [InvoiceController::class, 'store']);
Route::post('/audit/log', [InvoiceController::class, 'clientAuditLog']);
Route::get('/invoice/do-items/{doId}', [InvoiceController::class, 'getDoItems']);
require __DIR__ . '/auth.php';

require __DIR__ . '/auth.php';

// Delivery Order Routes
Route::get('/delivery/create', [DeliveryOrderController::class, 'create'])->name('delivery.create');
Route::post('/delivery/insert', [DeliveryOrderController::class, 'insert'])->name('delivery.insert');
Route::get('/delivery/list', [DeliveryOrderController::class, 'list'])->name('delivery.list');
Route::get('/delivery/{id}', [DeliveryOrderController::class, 'show'])->name('delivery.show');
Route::delete('/delivery/{id}/delete', [DeliveryOrderController::class, 'destroy'])->name('delivery.destroy');
Route::get('/delivery/file/{id}/{type}', [DeliveryOrderController::class, 'showFile'])->name('delivery.file');
Route::post('/delivery/check-po', [DeliveryOrderController::class, 'checkPoNumber'])->name('delivery.checkPo');
