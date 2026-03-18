<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    // Se llama el HasFactory //
    use HasFactory;

    protected $table = 'prestamo'; // Nombre de la tabla en la BD ala que el modelo hace referencia. 
    protected $primaryKey = 'id_prestamo'; // Atributo de la llave primaria asociado con la tabla.
    public $incrementing = true; // Indica si el id del modelo es autoincrementable.
    protected $keyType = "int"; // Indica si el tipo de dato del id es entero.
    
    protected $fk_id_empleado;
    protected $fecha_solicitud; 
    protected $monto;
    protected $plazo;
    protected $fecha_aprob;
    protected $tasa_mensual;
    protected $pago_fijo_cap;
    protected $fecha_ini_desc;
    protected $fecha_fin_desc;
    protected $saldo_actual;
    protected $estado;
    
    protected $fillable = [
        "fk_id_empleado",
        "fecha_solicitud",
        "monto",
        "plazo",
        "fecha_aprob",
        "tasa_mensual",
        "pago_fijo_cap",
        "fecha_ini_desc",
        "fecha_fin_desc",
        "saldo_actual",
        "estado"
    ];

    public $timestamps = false;
}
