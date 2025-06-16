<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\RentaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\CalendarioController;
use App\Http\Controllers\FacturaController;
use App\Models\Producto;
use App\Models\Renta;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Aquí es donde puedes registrar las rutas web para tu aplicación. Estas
| rutas son cargadas por el RouteServiceProvider dentro de un grupo que
| contiene el middleware "web".
|
*/

Route::middleware(['auth'])->group(function () {
    // Productos
    Route::resource('productos', ProductoController::class)->names([
        'index' => 'productos.index',
        'create' => 'productos.crear',
        'store' => 'productos.guardar',
        'show' => 'productos.mostrar',
        'edit' => 'productos.editar',
        'update' => 'productos.actualizar',
        'destroy' => 'productos.eliminar'
    ]);
    
    // Clientes
    Route::resource('clientes', ClienteController::class)->names([
        'index' => 'clientes.index',
        'create' => 'clientes.crear',
        'store' => 'clientes.guardar',
        'show' => 'clientes.mostrar',
        'edit' => 'clientes.editar',
        'update' => 'clientes.actualizar',
        'destroy' => 'clientes.eliminar'
    ]);
    
    // Rentas
    Route::resource('rentas', RentaController::class)->names([
        'index' => 'rentas.index',
        'create' => 'rentas.crear',
        'store' => 'rentas.guardar',
        'show' => 'rentas.mostrar',
        'edit' => 'rentas.editar',
        'update' => 'rentas.actualizar',
        'destroy' => 'rentas.eliminar'
    ]);
    
    // Devolución de rentas
    Route::post('/rentas/{renta}/devolver', [RentaController::class, 'devolver'])
        ->name('rentas.devolver');
    
    // Pagos
    Route::post('/rentas/{renta}/pagos', [PagoController::class, 'store'])
        ->name('pagos.store');
    
    // Calendario
    Route::get('/calendario', [CalendarioController::class, 'index'])->name('calendario.index');
    
    // Facturas
    Route::get('/rentas/{renta}/factura', [FacturaController::class, 'mostrar'])->name('facturas.mostrar');
    Route::get('/rentas/{renta}/factura/descargar', [FacturaController::class, 'descargar'])->name('facturas.descargar');
    
    // Dashboard
    Route::get('/dashboard', function () {
        $productosDisponibles = Producto::disponibles()->count();
        $productosRentados = Producto::rentados()->count();
        $rentasActivas = Renta::where('estado', '!=', 'devuelto')->count();
        $rentasAtrasadas = Renta::where('fecha_devolucion', '<', now())
            ->where('estado', '!=', 'devuelto')
            ->count();
            
        return view('dashboard', compact(
            'productosDisponibles', 
            'productosRentados',
            'rentasActivas',
            'rentasAtrasadas'
        ));
    })->name('dashboard');
});

// API para eventos del calendario
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/calendario/eventos', [CalendarioController::class, 'eventos']);
});