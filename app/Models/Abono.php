<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Abono extends Model
{
    // Se llama el HasFactory //
    use HasFactory;

    protected $table = 'abono'; // Nombre de la tabla en la BD ala que el modelo hace referencia. 
    protected $primaryKey = 'id_abono'; // Atributo de la llave primaria asociado con la tabla.
    public $incrementing = true; // Indica si el id del modelo es autoincrementable.
    protected $keyType = "int"; // Indica si el tipo de dato del id es entero.
    
    protected $fk_id_prestamo;
    protected $num_abono;
    protected $fecha;
    protected $monto_capital;
    protected $monto_interes;
    protected $monto_cobrado;
    protected $saldo_pendiente;

    protected $fillable = [
        "fk_id_prestamo",
        "num_abono",
        "fecha",
        "monto_capital",
        "monto_interes",
        "monto_cobrado",
        "saldo_pendiente"
    ];    
    
    public $timestamps = false; // Agrega esta línea
}
