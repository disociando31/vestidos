<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemRenta extends Model
{
    protected $fillable = [
        'renta_id',
        'producto_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
        'iva',
        'descuento',
        'total',
        'atributos'  // JSON con detalles específicos
    ];
    
    protected $casts = [
        'atributos' => 'array'
    ];
    
    public function renta(): BelongsTo
    {
        return $this->belongsTo(Renta::class);
    }
    
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
