<?php

namespace App\Http\Controllers;

use App\Models\Puesto;
use App\Models\Empleado;
use App\Models\Detalle_Emp_Puesto;
use App\Models\Prestamo;

use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogosController extends Controller
{
    // ── HOME ──────────────────────────────────────────────────────
    public function home(): View
    {
        return view('home', ["breadcrumbs" => []]);
    }

    // ── PUESTOS ───────────────────────────────────────────────────
    public function puestosGet(): View
    {
        $puestos = Puesto::all();
        return view('catalogos/puestosGet', [
            'puestos' => $puestos,
            "breadcrumbs" => [
                "Inicio"  => url("/"),
                "Puestos" => url("/catalogos/puestos")
            ]
        ]);
    }

    public function puestosAgregarGet(): View
    {
        return view('catalogos/puestosAgregarGet', [
            "breadcrumbs" => [
                "Inicio"  => url("/"),
                "Puestos" => url("/catalogos/puestos"),
                "Agregar" => url("/catalogos/puestos/agregar")
            ]
        ]);
    }

    public function puestosAgregarPost(Request $request)
    {
        $puesto = new Puesto([
            "nombre" => strtoupper($request->input("nombre")),
            "sueldo" => $request->input("sueldo")
        ]);
        $puesto->save();

        return redirect("/catalogos/puestos")
            ->with('success', "Puesto agregado correctamente");
    }

    // ── EMPLEADOS ─────────────────────────────────────────────────
    public function empleadosGet(): View
    {
        $empleados = Empleado::all();
        return view('catalogos/empleadosGet', [
            'empleados' => $empleados,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Empleados" => url("/empleados")
            ]
        ]);
    }

    public function empleadosAgregarGet(): View
    {
        $puestos = Puesto::all();
        return view('catalogos/empleadosAgregarGet', [
            "puestos" => $puestos,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Empleados" => url("/empleados"),
                "Agregar"   => url("/empleados/agregar")
            ]
        ]);
    }

    public function empleadosAgregarPost(Request $request)
    {
        $empleado = new Empleado([
            "nombre"        => strtoupper($request->input("nombre")),
            "fecha_ingreso" => $request->input("fecha_ingreso"),
            "activo"        => $request->input("activo"),
        ]);
        $empleado->save();

        $puesto = new Detalle_Emp_Puesto([
            "fk_id_empleado" => $empleado->id_empleado,
            "fk_id_puesto"   => $request->input("puesto"),
            "fecha_inicio"   => $request->input("fecha_ingreso")
        ]);
        $puesto->save();

        return redirect("/empleados");
    }

    public function empleadosModificarGet($id): View
    {
        $empleado = Empleado::find($id);
        return view('catalogos/empleadosModificarGet', [
            "empleado" => $empleado,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Empleados" => url("/empleados"),
                "Modificar" => url("/empleados/" . $id . "/modificar") 
            ]
        ]);
    }

    public function empleadosModificarPost(Request $request, $id)
    {
        $empleado = Empleado::find($id);
        $empleado->nombre        = strtoupper($request->input("nombre"));
        $empleado->fecha_ingreso = $request->input("fecha_ingreso");
        $empleado->activo        = $request->input("activo");
        $empleado->save();

        return redirect("/empleados")->with("success", "Empleado actualizado");
    }

public function empleadoPrestamosGet($id): View
    {
        $empleado  = Empleado::find($id);
        $prestamos = Prestamo::where("fk_id_empleado", $id)->get();

        return view('catalogos/empleadosPrestamosGet', [
            "empleado"  => $empleado,
            "prestamos" => $prestamos,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Empleados" => url("/empleados"),
                "Préstamos" => url("/empleados/" . $id . "/prestamos") 
            ]
        ]);
    }

    // ── REPORTES ──────────────────────────────────────────────────
    public function reportesGet(): View
    {
        $prestamos = Prestamo::all();

        foreach ($prestamos as $prestamo) {
            $empleado = Empleado::find($prestamo->fk_id_empleado);
            $prestamo->nombre_empleado = $empleado ? $empleado->nombre : "Sin nombre";
        }

        return view('catalogos/reportesGet', [
            "prestamos" => $prestamos,
            "breadcrumbs" => [
                "Inicio"   => url("/"),
                "Reportes" => url("/reportes")
            ]
        ]);
    }

    public function empleadoPuestosGet($id): View
    {
        $empleado = Empleado::find($id);

        // Usando 'det_emp_puesto' consistentemente
        $puestos = Detalle_Emp_Puesto::join('puesto', 'puesto.id_puesto', '=', 'det_emp_puesto.fk_id_puesto')
            ->select('puesto.nombre', 'det_emp_puesto.fecha_inicio')
            ->where('det_emp_puesto.fk_id_empleado', $id)
            ->orderBy('det_emp_puesto.fecha_inicio', 'desc')
            ->get();

        return view('catalogos/empleadosPuestosGet', [
            "empleado" => $empleado,
            "puestos"  => $puestos,
            "breadcrumbs" => [
                "Inicio"    => url("/"),
                "Empleados" => url("/empleados"),
                "Puestos"   => url("/empleados/{$id}/puestos")
            ]
        ]);
    }

} // Fin CatalogosController