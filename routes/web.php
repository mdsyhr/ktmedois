<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\DeliveryOrderController;
use Illuminate\Support\Facades\Route;
use App\Models\Supplier;

Route::get('/', function () {
    return redirect()->route('login');
});

// Vendor Dashboard
Route::get('/vendor/dashboard', function () {
    $supplier = Supplier::where('User_ID', auth()->user()->User_ID)->first();

    $totalDO = $supplier ? \App\Models\DeliveryOrder::where('Supplier_ID', $supplier->Supplier_ID)->count() : 0;

    $pendingDO = $supplier ? \App\Models\DeliveryOrder::where('Supplier_ID', $supplier->Supplier_ID)
        ->where('Status', 'Pending')->count() : 0;

    $totalInvoice = $supplier ? \App\Models\Invoice::whereHas('deliveryOrder', function($q) use ($supplier) {
    $q->where('Supplier_ID', $supplier->Supplier_ID);
    })->count() : 0;

    $doStatuses = $supplier ? \App\Models\DeliveryOrder::where('Supplier_ID', $supplier->Supplier_ID)
        ->selectRaw('Status, COUNT(*) as count')
        ->groupBy('Status')
        ->pluck('count', 'Status') : collect();

    return view('usermanagement.vendor.Vendor_DashboardView', compact(
        'supplier', 'totalDO', 'pendingDO', 'totalInvoice', 'doStatuses'
    ));
})->middleware(['auth'])->name('vendor.dashboard');

// Default dashboard redirect
Route::get('/dashboard', function () {
    return redirect()->route('vendor.dashboard');
})->middleware(['auth'])->name('dashboard');

// Profile Routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Invoice Routes
Route::post('/invoice/extract-data', [InvoiceController::class, 'extractData']);
Route::get('/invoicelist', [InvoiceController::class, 'index'])->name('invoice.list');
Route::get('/invoice', [InvoiceController::class, 'index']);
Route::get('/invoice/create/{doId}', [InvoiceController::class, 'create']);
Route::post('/invoice/store', [InvoiceController::class, 'store']);
Route::post('/audit/log', [InvoiceController::class, 'clientAuditLog']);
Route::get('/invoice/do-items/{doId}', [InvoiceController::class, 'getDoItems']);

// Delivery Order Routes
Route::get('/delivery/create', [DeliveryOrderController::class, 'create'])->name('delivery.create');
Route::post('/delivery/insert', [DeliveryOrderController::class, 'insert'])->name('delivery.insert');
Route::get('/delivery/list', [DeliveryOrderController::class, 'list'])->name('delivery.list');
Route::get('/delivery/{id}', [DeliveryOrderController::class, 'show'])->name('delivery.show');
Route::delete('/delivery/{id}/delete', [DeliveryOrderController::class, 'destroy'])->name('delivery.destroy');
Route::get('/delivery/file/{id}/{type}', [DeliveryOrderController::class, 'showFile'])->name('delivery.file');
Route::post('/delivery/check-po', [DeliveryOrderController::class, 'checkPoNumber'])->name('delivery.checkPo');

require __DIR__ . '/auth.php';