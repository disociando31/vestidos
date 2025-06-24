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
*/

// Página principal → Calendario
Route::get('/', [CalendarioController::class, 'index'])->name('calendario.index');
Route::get('/calendario/eventos', [CalendarioController::class, 'eventos'])->name('calendario.eventos');

// Productos
Route::resource('productos', ProductoController::class)->names([
    'index'   => 'productos.index',
    'create'  => 'productos.crear',
    'store'   => 'productos.guardar',
    'show'    => 'productos.mostrar',
    'edit'    => 'productos.editar',
    'update'  => 'productos.actualizar',
    'destroy' => 'productos.eliminar',
]);

// Clientes
Route::resource('clientes', ClienteController::class)->names([
    'index'   => 'clientes.index',
    'create'  => 'clientes.crear',
    'store'   => 'clientes.guardar',
    'show'    => 'clientes.mostrar',
    'edit'    => 'clientes.editar',
    'update'  => 'clientes.actualizar',
    'destroy' => 'clientes.eliminar',
]);

// Rentas
Route::resource('rentas', RentaController::class)->names([
    'index'   => 'rentas.index',
    'create'  => 'rentas.crear',
    'store'   => 'rentas.guardar',
    'show'    => 'rentas.mostrar',
    'edit'    => 'rentas.editar',
    'update'  => 'rentas.actualizar',
    'destroy' => 'rentas.eliminar',
]);

// Devolución de rentas
Route::post('/rentas/{renta}/devolver', [RentaController::class, 'devolver'])->name('rentas.devolver');

// Pagos
Route::prefix('pagos')->group(function () {
    Route::get('/', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/{pago}', [PagoController::class, 'show'])->name('pagos.show');
});

// Facturas
Route::get('/rentas/{renta}/factura', [FacturaController::class, 'mostrar'])->name('facturas.mostrar');
Route::get('/rentas/{renta}/factura/descargar', [FacturaController::class, 'descargar'])->name('facturas.descargar');

// Dashboard (estadísticas)
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

// API para eventos del calendario (FullCalendar)
Route::get('/calendario/eventos', [CalendarioController::class, 'eventos'])->name('calendario.eventos');
Route::prefix('pagos')->group(function () {
    Route::get('/', [PagoController::class, 'index'])->name('pagos.index');
    Route::get('/reporte', [PagoController::class, 'reporte'])->name('pagos.reporte');
    Route::get('/crear/{renta}', [PagoController::class, 'create'])->name('pagos.crear');
    Route::post('/guardar/{renta}', [PagoController::class, 'store'])->name('pagos.store');
    Route::delete('/{pago}', [PagoController::class, 'destroy'])->name('pagos.eliminar');
});
