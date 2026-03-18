<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Puesto extends Model
{
    // Se llama el HasFactory //
    use HasFactory;

    protected $table = 'puesto'; // Nombre de la tabla en la BD ala que el modelo hace referencia. 
    protected $primaryKey = 'id_puesto'; // Atributo de la llave primaria asociado con la tabla.
    public $incrementing = true; // Indica si el id del modelo es autoincrementable.
    protected $keyType = "int"; // Indica si el tipo de dato del id es entero.
    
    protected $nombre; // Nombre del campo ara recibir el nombre del puesto.
    protected $sueldo; // Nombre del campo para recibir el sueldo.

    protected $fillable = [
        "nombre",
        "sueldo"
    ];

    public $timestamps = false;
}
