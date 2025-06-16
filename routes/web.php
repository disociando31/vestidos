<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CalendarController;
use App\Models\Product;
use App\Models\Rental;

Route::middleware(['auth'])->group(function () {
    // Productos
    Route::resource('products', ProductController::class);
    
    // Clientes
    Route::resource('customers', CustomerController::class);
    
    // Rentas
    Route::resource('rentals', RentalController::class);
    Route::post('/rentals/{rental}/return', [RentalController::class, 'return'])
        ->name('rentals.return');
    
    // Pagos
    Route::post('/rentals/{rental}/payments', [PaymentController::class, 'store'])
        ->name('payments.store');
    
    // Calendario
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    
    // Dashboard
    Route::get('/dashboard', function () {
        $availableProducts = Product::available()->count();
        $rentedProducts = Product::rented()->count();
        $activeRentals = Rental::where('status', '!=', 'returned')->count();
        $lateRentals = Rental::where('return_date', '<', now())
            ->where('status', '!=', 'returned')
            ->count();
            
        return view('dashboard', compact(
            'availableProducts', 
            'rentedProducts',
            'activeRentals',
            'lateRentals'
        ));
    })->name('dashboard');
});

// routes/api.php
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/calendar/events', [CalendarController::class, 'events']);
});
// routes/web.php
Route::get('/rentals/{rental}/invoice', [InvoiceController::class, 'show'])->name('rentals.invoice');
Route::get('/rentals/{rental}/invoice/download', [InvoiceController::class, 'download'])->name('rentals.invoice.download');