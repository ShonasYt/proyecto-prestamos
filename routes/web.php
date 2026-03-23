<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogosController;
use App\Http\Controllers\MovimientosController;

// ── CATÁLOGOS ─────────────────────────────────────────────────────
Route::get('/', [CatalogosController::class, "home"]);

Route::get ("/catalogos/puestos",          [CatalogosController::class, "puestosGet"]);
Route::get ("/catalogos/puestos/agregar",  [CatalogosController::class, "puestosAgregarGet"]);
Route::post("/catalogos/puestos/agregar",  [CatalogosController::class, "puestosAgregarPost"]);

Route::get ("/empleados",                  [CatalogosController::class, "empleadosGet"]);
Route::get ("/empleados/agregar",          [CatalogosController::class, "empleadosAgregarGet"]);
Route::post("/empleados/agregar",          [CatalogosController::class, "empleadosAgregarPost"]);

Route::get ("/empleados/{id}/modificar",   [CatalogosController::class, "empleadosModificarGet"]);
Route::post("/empleados/{id}/modificar",   [CatalogosController::class, "empleadosModificarPost"]);

Route::get ("/empleados/{id}/prestamos",   [CatalogosController::class, "empleadoPrestamosGet"]);

Route::get ("/reportes",                   [CatalogosController::class, "reportesGet"]);

// ── MOVIMIENTOS ───────────────────────────────────────────────────
Route::get ("/movimientos/prestamos",          [MovimientosController::class, "prestamosGet"]);
Route::get ("/movimientos/prestamos/agregar",  [MovimientosController::class, "prestamosAgregarGet"]);
Route::post("/movimientos/prestamos/agregar",  [MovimientosController::class, "prestamosAgregarPost"]);
Route::get ("/movimientos/prestamos/ver/{id}", [MovimientosController::class, "prestamosVerGet"]);
 
Route::get ("/movimientos/prestamos/{id}/abonos/agregar", [MovimientosController::class, "abonosAgregarGet"])
    ->name("movimientos.abonosAgregarGet");
Route::post("/movimientos/prestamos/{id}/abonos/agregar", [MovimientosController::class, "abonosAgregarPost"])
    ->name("movimientos.abonosAgregarPost");

Route::get("/empleados/{id}/puestos", [CatalogosController::class, "empleadoPuestosGet"]);