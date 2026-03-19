<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogosController; // <-- Importamos tu controlador
use App\Http\Controllers\MovimientosController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Reemplazamos la ruta por defecto (welcome) por la tuya
Route::get('/', [CatalogosController::class, "home"]);

// Ruta para el catálogo de puestos
Route::get("/catalogos/puestos", [CatalogosController::class, "puestosGet"]);

//Ruta para el catálogo de Empleados
Route::get ("/empleados",[CatalogosController::class, "empleadosGet"]);
Route::get ("/empleados/agregar",[CatalogosController::class, "empleadosAgregarGet"]);
Route::post ("/empleados/agregar",[CatalogosController::class, "empleadosAgregarPost"]);

// Prestamos
Route::get("/movimientos/prestamos",              [MovimientosController::class, "prestamosGet"]);
Route::get("/movimientos/prestamos/agregar",      [MovimientosController::class, "prestamosAgregarGet"]);
Route::post("/movimientos/prestamos/agregar",     [MovimientosController::class, "prestamosAgregarPost"]);
Route::get("/movimientos/prestamos/ver/{id}",     [MovimientosController::class, "prestamosVerGet"]);
 
