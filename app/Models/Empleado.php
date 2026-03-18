<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    // Se llama el HasFactory //
    use HasFactory;

    protected $table = 'empleado'; // Nombre de la tabla en la BD ala que el modelo hace referencia. 
    protected $primaryKey = 'id_empleado'; // Atributo de la llave primaria asociado con la tabla.
    public $incrementing = true; // Indica si el id del modelo es autoincrementable.
    protected $keyType = "int"; // Indica si el tipo de dato del id es entero.
    
    protected $nombre; // Nombre del campo ara recibir el nombre del empleado.
    protected $fecha_ingreso; // Nombre del campo para recibir la fecha.
    protected $activo;

    protected $fillable = [
        "nombre",
        "fecha_ingreso",
        "activo"
    ];

    public $timestamps = false;
}
