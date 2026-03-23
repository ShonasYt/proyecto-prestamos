<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Detalle_Emp_Puesto extends Model
{
    // Se llama el HasFactory //
    use HasFactory;

    protected $table = 'det_emp_puesto'; // Nombre de la tabla en la BD ala que el modelo hace referencia. 
    protected $primaryKey = 'id_det_emp_puesto'; // Atributo de la llave primaria asociado con la tabla.
    public $incrementing = true; // Indica si el id del modelo es autoincrementable.
    protected $keyType = "int"; // Indica si el tipo de dato del id es entero.
    
    protected $fk_id_empleado;
    protected $fk_id_puesto;    
    protected $fecha_inicio;
    protected $fecha_fin;
    
    protected $fillable = [
        "fk_id_empleado",
        "fk_id_puesto",
        "fecha_inicio",
        "fecha_fin"
    ];

    public $timestamps = false;
}
