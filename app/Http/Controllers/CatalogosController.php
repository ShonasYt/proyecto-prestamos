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

class CatalogosController extends Controller
{
    //Para manejarnos al Inicio//
    public function home(): View
    {
        return view('home', ["breadcrumbs"=>[]]);
    }

    

    //Al precionar el boton de puestos del Catalogo del SideBar se muestran los puestos//
    public function puestosGet(): View
    {
        $puestos = Puesto::all();
        return view('catalogos/puestosGet', [
            'puestos' => $puestos,
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Puestos"=>url("/catalogos/puestos")
            ]
        ]);
    }

    //Al precionar el boton de empleados del Catalogo del SideBar se muestran los empleados//
    public function empleadosGet(): View
    {
        $empleados = Empleado::all();
        return view('catalogos/empleadosGet', [
            'empleados' => $empleados,
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Empleados"=>url("/empleados")
            ]
        ]);
    }

    //Al precionar el boton de agregar en el apartado del catalago de empleados//
    public function empleadosAgregarGet(): View
    {
        $puestos=Puesto::all();
        return view('catalogos/empleadosAgregarGet',[
            "puestos"=>$puestos,
            "breadcrumbs"=>[
                "Inicio"=>url("/"),
                "Empleados"=>url("/empleados"),
                "Agregar"=>url("/empleados/agregar")
            ]
        ]);
    }

    public function empleadosAgregarPost(Request $request)
    {
        $nombre=$request->input("nombre");
        $fecha_ingreso=$request->input("fecha_ingreso");
        $activo=$request->input("activo");

        $empleado=new Empleado([
            "nombre"=>strtoupper($nombre),
            "fecha_ingreso"=>$fecha_ingreso,
            "activo"=>$activo,
        ]);

        $empleado->save();

        $puesto=new Detalle_Emp_Puesto([
            "fk_id_empleado" => $empleado->id_empleado,
            "fk_id_puesto" => $request->input("puesto"),
            "fecha_inicio" => $fecha_ingreso
        ]);

        $puesto->save();
        return redirect("/empleados"); // redirige al listado de
    }




} //Fin Catalogos//