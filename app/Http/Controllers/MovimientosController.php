<?php

namespace App\Http\Controllers;

//Lines para extender el uso de los modelos//
use App\Models\Puesto;
use App\Models\Empleado;
use App\Models\Abono;
use App\Models\Detalle_Emp_Puesto;
use App\Models\Prestamo;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MovimientosController extends Controller
{
    //Al presionar el boton de Prestamos del SideBar se muestran los prestamos//
    public function prestamosGet(): View
    {
        $prestamos = Prestamo::all();
 
        //Se busca el nombre del empleado para cada prestamo//
        foreach ($prestamos as $prestamo) {
            $empleado = Empleado::find($prestamo->fk_id_empleado);
            $prestamo->nombre_empleado = $empleado->nombre;
        }
 
        return view('movimientos/prestamosGet', [
            'prestamos' => $prestamos,
            "breadcrumbs"=>[
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos")
            ]
        ]);
    }
 
    //Al presionar el boton de agregar en el apartado de prestamos//
    public function prestamosAgregarGet(): View
    {
        $empleados = Empleado::all();
        return view('movimientos/prestamosAgregarGet', [
            "empleados" => $empleados,
            "breadcrumbs"=>[
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos"),
                "Agregar"   => url("/movimientos/prestamos/agregar")
            ]
        ]);
    }
 
    //Se guarda el prestamo y se generan los abonos quincenales automaticamente//
    public function prestamosAgregarPost(Request $request)
    {
        $fk_id_empleado  = $request->input("fk_id_empleado");
        $fecha_solicitud = $request->input("fecha_solicitud");
        $monto           = (float) $request->input("monto");
        $plazo           = (int)   $request->input("plazo");
        $tasa_mensual    = (float) $request->input("tasa_mensual");
        $fecha_aprob     = $request->input("fecha_aprob");
        $fecha_ini_desc  = $request->input("fecha_ini_desc");
 
        //Calculo de tasa quincena y pago fijo con formula de amortizacion//
        $tasa_quincena = $tasa_mensual / 2 / 100;
 
        if ($tasa_quincena == 0) {
            $pago_fijo = $monto / $plazo;
        } else {
            $pago_fijo = $monto * ($tasa_quincena * pow(1 + $tasa_quincena, $plazo))
                                / (pow(1 + $tasa_quincena, $plazo) - 1);
        }
 
        //Calculo de la fecha fin sumando N quincenas a la fecha de inicio//
        $fecha_fin = $this->sumarQuincenas($fecha_ini_desc, $plazo);
 
        //Se guarda el prestamo//
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
 
        //Se generan los abonos quincenales uno por uno//
        $saldo       = $monto;
        $fecha_abono = new DateTime($fecha_ini_desc);
 
        for ($i = 1; $i <= $plazo; $i++) {
            $interes = round($saldo * $tasa_quincena, 2);
            $capital = round($pago_fijo - $interes, 2);
 
            //En el ultimo abono se liquida el saldo restante//
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
 
            //Se avanza a la siguiente quincena//
            $fecha_abono = $this->siguienteQuincena($fecha_abono);
        }
 
        return redirect("/movimientos/prestamos");
    }
 
    //Se muestra el detalle de un prestamo con su tabla de abonos//
    public function prestamosVerGet($id): View
    {
        $prestamo = Prestamo::find($id);
 
        //Se busca el nombre del empleado//
        $empleado = Empleado::find($prestamo->fk_id_empleado);
        $prestamo->nombre_empleado = $empleado->nombre;
 
        //Se obtienen los abonos del prestamo ordenados//
        $abonos = Abono::where('fk_id_prestamo', $id)
            ->orderBy('num_abono', 'asc')
            ->get();
 
        return view('movimientos/prestamosVerGet', [
            'prestamo' => $prestamo,
            'abonos'   => $abonos,
            "breadcrumbs"=>[
                "Inicio"    => url("/"),
                "Préstamos" => url("/movimientos/prestamos"),
                "Detalle"   => url("/movimientos/prestamos/ver/{$id}")
            ]
        ]);
    }
 
    //Avanza una fecha a la siguiente quincena (dia 1 o dia 16 de cada mes)//
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
 
    //Suma N quincenas a una fecha y devuelve la fecha resultante//
    private function sumarQuincenas(string $fecha_inicio, int $n): string
    {
        $fecha = new DateTime($fecha_inicio);
        for ($i = 0; $i < $n; $i++) {
            $fecha = $this->siguienteQuincena($fecha);
        }
        return $fecha->format('Y-m-d');
    }

}