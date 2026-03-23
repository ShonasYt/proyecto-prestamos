<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\Empleado;
use App\Models\Abono;
use App\Models\Detalle_Emp_Puesto;
use App\Models\Prestamo;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovimientosController extends Controller
{
    // ── PRÉSTAMOS ─────────────────────────────────────────────────
    public function prestamosGet(): View
    {
        $prestamos = Prestamo::all();

        foreach ($prestamos as $prestamo) {
            $empleado = Empleado::find($prestamo->fk_id_empleado);
            $prestamo->nombre_empleado = $empleado->nombre;
        }

        return view('movimientos/prestamosGet', [
            'prestamos' => $prestamos,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos")
            ]
        ]);
    }

    public function prestamosAgregarGet(): View
    {
        $empleados = Empleado::all();

        foreach ($empleados as $empleado) {
            $detalle = Detalle_Emp_Puesto::where('fk_id_empleado', $empleado->id_empleado)
                ->latest('fecha_inicio')
                ->first();
            $empleado->sueldo_puesto   = $detalle ? Puesto::find($detalle->fk_id_puesto)->sueldo : 0;
            $empleado->fecha_ingreso_f = $empleado->fecha_ingreso;
        }

        return view('movimientos/prestamosAgregarGet', [
            "empleados" => $empleados,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos"),
                "Agregar"   => url("/movimientos/prestamos/agregar")
            ]
        ]);
    }

    public function prestamosAgregarPost(Request $request)
    {
        $fk_id_empleado  = $request->input("fk_id_empleado");
        $fecha_solicitud = $request->input("fecha_solicitud");
        $monto           = (float) $request->input("monto");
        $plazo           = (int)   $request->input("plazo");
        $tasa_mensual    = (float) $request->input("tasa_mensual");
        $fecha_aprob     = $request->input("fecha_aprob");
        $fecha_ini_desc  = $request->input("fecha_ini_desc");

        // ── VALIDACIONES ──────────────────────────────────────────

        $empleado = Empleado::find($fk_id_empleado);

        // 1. Antigüedad mínima de 1 año
        $fechaIngreso = new DateTime($empleado->fecha_ingreso);
        $hoy          = new DateTime();
        $antiguedad   = $fechaIngreso->diff($hoy);
        if ($antiguedad->y < 1) {
            return back()->withErrors([
                'fk_id_empleado' => 'El empleado debe tener al menos 1 año de antigüedad.'
            ])->withInput();
        }

        // 2. Sin préstamo activo
        $prestamoActivo = Prestamo::where('fk_id_empleado', $fk_id_empleado)
            ->where('estado', 'ACTIVO')
            ->exists();
        if ($prestamoActivo) {
            return back()->withErrors([
                'fk_id_empleado' => 'El empleado ya tiene un préstamo activo.'
            ])->withInput();
        }

        // 3. Monto máximo: 6 meses de salario
        $detalle     = Detalle_Emp_Puesto::where('fk_id_empleado', $fk_id_empleado)
            ->latest('fecha_inicio')
            ->first();
        $sueldo      = $detalle ? Puesto::find($detalle->fk_id_puesto)->sueldo : 0;
        $montoMaximo = $sueldo * 6;
        if ($monto > $montoMaximo) {
            return back()->withErrors([
                'monto' => "El monto no puede superar 6 meses de salario (\${$montoMaximo})."
            ])->withInput();
        }

        // ── GUARDAR ───────────────────────────────────────────────
        $tasa_quincena = $tasa_mensual / 2 / 100;

        if ($tasa_quincena == 0) {
            $pago_fijo = $monto / $plazo;
        } else {
            $pago_fijo = $monto * ($tasa_quincena * pow(1 + $tasa_quincena, $plazo))
                                / (pow(1 + $tasa_quincena, $plazo) - 1);
        }

        $fecha_fin = $this->sumarQuincenas($fecha_ini_desc, $plazo);

        $prestamo = new Prestamo([
            "fk_id_empleado"  => $fk_id_empleado,
            "fecha_solicitud" => $fecha_solicitud,
            "monto"           => $monto,
            "plazo"           => $plazo,
            "fecha_aprob"     => $fecha_aprob,
            "tasa_mensual"    => $tasa_mensual,
            "pago_fijo_cap"   => round($pago_fijo, 2),
            "fecha_ini_desc"  => $fecha_ini_desc,
            "fecha_fin_desc"  => $fecha_fin,
            "saldo_actual"    => $monto,
            "estado"          => "ACTIVO"
        ]);
        $prestamo->save();

        $saldo       = $monto;
        $fecha_abono = new DateTime($fecha_ini_desc);

        for ($i = 1; $i <= $plazo; $i++) {
            $interes = round($saldo * $tasa_quincena, 2);
            $capital = round($pago_fijo - $interes, 2);

            if ($i === $plazo) {
                $capital = $saldo;
                $interes = round($saldo * $tasa_quincena, 2);
            }

            $cobrado = round($capital + $interes, 2);
            $saldo   = round($saldo - $capital, 2);
            if ($saldo < 0) $saldo = 0;

            $abono = new Abono([
                "fk_id_prestamo"  => $prestamo->id_prestamo,
                "num_abono"       => $i,
                "fecha"           => $fecha_abono->format('Y-m-d'),
                "monto_capital"   => $capital,
                "monto_interes"   => $interes,
                "monto_cobrado"   => $cobrado,
                "saldo_pendiente" => $saldo
            ]);
            $abono->save();

            $fecha_abono = $this->siguienteQuincena($fecha_abono);
        }

        return redirect("/movimientos/prestamos");
    }

    public function prestamosVerGet($id): View
    {
        $prestamo = Prestamo::find($id);

        $empleado = Empleado::find($prestamo->fk_id_empleado);
        $prestamo->nombre_empleado = $empleado->nombre;

        $abonos = Abono::where('fk_id_prestamo', $id)
            ->orderBy('num_abono', 'asc')
            ->get();

        return view('movimientos/prestamosVerGet', [
            'prestamo' => $prestamo,
            'abonos'   => $abonos,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos"),
                "Detalle"   => url("/movimientos/prestamos/ver/{$id}")
            ]
        ]);
    }

    // ── ABONOS ────────────────────────────────────────────────────
    public function crearAbono($id): View
    {
        $prestamo = Prestamo::find($id);
        $empleado = Empleado::find($prestamo->fk_id_empleado);
        $prestamo->nombre_empleado = $empleado->nombre;

        $abonos       = Abono::where("fk_id_prestamo", $id)->orderBy("num_abono")->get();
        $total_pagado = $abonos->sum("monto_cobrado");

        $ultimo    = $abonos->last();
        $num_abono = $ultimo ? $ultimo->num_abono + 1 : 1;

        return view('movimientos/crearAbono', [
            "prestamo"     => $prestamo,
            "abonos"       => $abonos,
            "total_pagado" => $total_pagado,
            "num_abono"    => $num_abono,
            "breadcrumbs"  => [
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos"),
                "Detalle"   => url("/movimientos/prestamos/ver/{$id}"),
                "Abonar"    => url("/movimientos/abonos/{$id}")
            ]
        ]);
    }

    public function guardarAbono(Request $request, $id)
    {
        $prestamo = Prestamo::find($id);

        if (!$prestamo) {
            return back()->with("error", "Préstamo no encontrado");
        }

        $request->validate([
            "monto" => "required|numeric|min:0.01"
        ]);

        $monto = (float) $request->input("monto");

        if ($monto < $prestamo->pago_fijo_cap) {
            return back()->withErrors([
                'monto' => "El monto mínimo es $" . number_format($prestamo->pago_fijo_cap, 2)
            ])->withInput();
        }

        $tasa    = $prestamo->tasa_mensual / 2 / 100;
        $interes = round($prestamo->saldo_actual * $tasa, 2);
        $capital = round($monto - $interes, 2);

        if ($capital <= 0) {
            return back()->withErrors([
                'monto' => "El monto no cubre el interés mínimo."
            ])->withInput();
        }

        $nuevoSaldo = round($prestamo->saldo_actual - $capital, 2);
        if ($nuevoSaldo < 0) $nuevoSaldo = 0;

        $ultimo    = Abono::where("fk_id_prestamo", $id)->orderBy("num_abono", "desc")->first();
        $num_abono = $ultimo ? $ultimo->num_abono + 1 : 1;

        Abono::create([
            "fk_id_prestamo"  => $id,
            "num_abono"       => $num_abono,
            "fecha"           => date("Y-m-d"),
            "monto_capital"   => $capital,
            "monto_interes"   => $interes,
            "monto_cobrado"   => round($monto, 2),
            "saldo_pendiente" => $nuevoSaldo
        ]);

        $prestamo->saldo_actual = $nuevoSaldo;
        if ($nuevoSaldo == 0) $prestamo->estado = "PAGADO";
        $prestamo->save();

        return redirect('/movimientos/prestamos/ver/' . $id)
            ->with("success", "Abono registrado correctamente");
    }

    // ── HELPERS ───────────────────────────────────────────────────
    private function siguienteQuincena(DateTime $fecha): DateTime
    {
        $dia  = (int) $fecha->format('d');
        $mes  = (int) $fecha->format('m');
        $anio = (int) $fecha->format('Y');

        if ($dia < 16) {
            return new DateTime("{$anio}-{$mes}-16");
        } else {
            $siguiente = new DateTime("{$anio}-{$mes}-01");
            $siguiente->modify('+1 month');
            return $siguiente;
        }
    }

    private function sumarQuincenas(string $fecha_inicio, int $n): string
    {
        $fecha = new DateTime($fecha_inicio);
        for ($i = 0; $i < $n; $i++) {
            $fecha = $this->siguienteQuincena($fecha);
        }
        return $fecha->format('Y-m-d');
    }

    // ── ABONOS ────────────────────────────────────────────────────────
    public function abonosAgregarGet($id_prestamo): View
    {
        $prestamo = Prestamo::find($id_prestamo);
        $empleado = Empleado::find($prestamo->fk_id_empleado);
        $prestamo->nombre_empleado = $empleado->nombre;

        $abonos    = Abono::where("fk_id_prestamo", $id_prestamo)->get();
        $num_abono = count($abonos) + 1;

        $pago_fijo_cap   = $prestamo->pago_fijo_cap;
        $tasa_quincena   = $prestamo->tasa_mensual / 2 / 100;
        $monto_interes   = round($prestamo->saldo_actual * $tasa_quincena, 2);
        $monto_cobrado   = round($pago_fijo_cap + $monto_interes, 2);
        $saldo_pendiente = round($prestamo->saldo_actual - $pago_fijo_cap, 2);

        // Ajuste para el último pago
        if ($saldo_pendiente < 0) {
            $pago_fijo_cap   = round($pago_fijo_cap + $saldo_pendiente, 2);
            $saldo_pendiente = 0;
            $monto_cobrado   = round($pago_fijo_cap + $monto_interes, 2);
        }

        return view('movimientos/abonosAgregarGet', [
            'prestamo'        => $prestamo,
            'num_abono'       => $num_abono,
            'pago_fijo_cap'   => $pago_fijo_cap,
            'monto_interes'   => $monto_interes,
            'monto_cobrado'   => $monto_cobrado,
            'saldo_pendiente' => $saldo_pendiente,
            'breadcrumbs'     => [
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos"),
                "Detalle"   => url("/movimientos/prestamos/ver/{$id_prestamo}"),
                "Agregar Abono" => ""
            ]
        ]);
    }

    public function abonosAgregarPost(Request $request)
    {
        $fk_id_prestamo  = $request->input("fk_id_prestamo");
        $num_abono       = $request->input("num_abono");
        $fecha           = $request->input("fecha");
        $monto_capital   = $request->input("monto_capital");
        $monto_interes   = $request->input("monto_interes");
        $monto_cobrado   = $request->input("monto_cobrado");
        $saldo_pendiente = $request->input("saldo_pendiente");

        $abono = new Abono([
            "fk_id_prestamo"  => $fk_id_prestamo,
            "num_abono"       => $num_abono,
            "fecha"           => $fecha,
            "monto_capital"   => $monto_capital,
            "monto_interes"   => $monto_interes,
            "monto_cobrado"   => $monto_cobrado,
            "saldo_pendiente" => $saldo_pendiente,
        ]);
        $abono->save();

        $prestamo               = Prestamo::find($fk_id_prestamo);
        $prestamo->saldo_actual = $saldo_pendiente;

        if ($saldo_pendiente < 0.01) {
            $prestamo->estado = "PAGADO";
        }
        $prestamo->save();

        return redirect("/movimientos/prestamos/ver/{$fk_id_prestamo}");
    }



} // Fin MovimientosController